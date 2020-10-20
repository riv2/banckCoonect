/*
using System.Collections.Generic;


namespace Atlas.Database
{
    public class XDBCatalogueMaterialEntry
    {
        public XDBCatalogueResource[] ResourceList { private set; get; }
        public XDBCatalogueMaterialEntry[] SubmaterialList { private set; get; }

        public XDBCatalogueMaterialEntry()
        {
            ResourceList = new XDBCatalogueResource[4];
            SubmaterialList = new XDBCatalogueMaterialEntry[4];
        }

        public XDBCatalogueMaterialEntry(XDBCatalogueResource[] resources, XDBCatalogueMaterialEntry[] submaterials)
        {
            ResourceList = resources;
            SubmaterialList = submaterials;
        }
    }

    public class XDBCatalogueEntry
    {
        /// <summary>
        /// Реализуемый продукт базы данных
        /// </summary>
        public XDBCatalogueProduct Product { private set; get; }

        /// <summary>
        /// Данные о цена и остатках продукта
        /// </summary>
        public XDBMemberStore Store { private set; get; }

        /// <summary>
        /// Данные о расположении продукта в иерархии вложенностей
        /// </summary>
        public XDBCatalogueHierarchy Hierarchy { private set; get; }

        /// <summary>
        /// Данные о материале продукта
        /// </summary>
        public XDBCatalogueMaterialEntry Material { private set; get; }

        /// <summary>
        /// Данные об основном ресурсе продукта
        /// </summary>
        public XDBCatalogueResource Resource { private set; get; }

        public XDBCatalogueEntry()
        {
        }

        public XDBCatalogueEntry(XDBCatalogueProduct product, XDBMemberStore store, XDBCatalogueHierarchy hierarchy, XDBCatalogueResource resoruce)
        {
            Product = product;
            Store = store;
            Hierarchy = hierarchy;
            Resource = resoruce;
        }
    }

    public class XDBCatalogueManager
    {
        #region Catalogues
        public static XDBCatalogue CreateCatalogue(XDatabaseAdapter connection, string memberUniq, string catalogueType)
        {
            XDBCatalogue catalogue = new XDBCatalogue();
            catalogue.Owner = memberUniq;
            catalogue.Name = "My " + catalogueType;
            catalogue.Type = catalogueType;
            catalogue.Access = XDBCatalogue.ONLY_ME;
            catalogue.DateCreated = XUtils.GetUnixTimestamp();

            XDBCatalogueProperty property = new XDBCatalogueProperty();
            property.Code = "PREFIX";
            property.Name = "Catalogue uniq resource prefix";
            property.Value = memberUniq.Replace("-", "").ToLower() + "_";

            try
            {
                connection.Insert(catalogue);
                catalogue = connection.Database.Table<XDBCatalogue>()
                    .Where(x => x.Owner == memberUniq && x.Type == catalogueType)
                    .FirstOrDefault();
                property.CatalogueUid = catalogue.Uid;
                connection.Insert(property);
            }
            catch(System.Exception ex)
            {
                catalogue = null;
                
            }
            return catalogue;
        }
        #endregion
        
        #region Hierarchy
        public static string CreateHierarchyUniq(XDatabaseAdapter connection, int size)
        {
            size = System.Math.Min(size, 15);
            string uniq = null;
            bool uniq_ready = false;
            while (!uniq_ready)
            {
                uniq = XU.GetRandomUniq(size);
                uniq_ready = (connection.Database.Table<XDBCatalogueHierarchy>().Where(x => x.Uniq == uniq).FirstOrDefault() == null);
            }
            return uniq;
        }

        public static XDBCatalogueHierarchy CreateProductHierarchy(XDatabaseAdapter connection, long catalogueUid, string owner, string hierarchy_path, int product_type)
        {
            XLogger.LogWarning("Create hierarchy: " + hierarchy_path);
            List<string> path = new List<string>(hierarchy_path.Split('/'));
            string hierarchy_uniq = CreateHierarchyUniq(connection, path.Count + 6);

            XDBCatalogueHierarchy hierarchy = new XDBCatalogueHierarchy();
            hierarchy.Uniq = hierarchy_uniq;
            hierarchy.Path = hierarchy_path;
            hierarchy.Name = path[path.Count - 1].ToLower();
            hierarchy.Name = hierarchy.Name.Substring(0, 1).ToUpper() + hierarchy.Name.Substring(1);
            hierarchy.MemberUniq = owner;
            hierarchy.ParentUniq = null;
            hierarchy.CatalogueUid = catalogueUid;
            hierarchy.ProductType = product_type;
            hierarchy.DateModified = XUtils.GetUnixTimestamp();
            hierarchy.DateDeleted = 0;

            if (path.Count > 1)
            {
                string parent_path = string.Join("/", path.GetRange(0, path.Count - 1).ToArray());
                XLogger.LogWarning("Required hierarchy: " + parent_path);
                XDBCatalogueHierarchy parent = connection.Database.Table<XDBCatalogueHierarchy>()
                    .Where(x => x.CatalogueUid == catalogueUid && x.Path == parent_path && x.ProductType == product_type)
                    .FirstOrDefault();

                if (parent == null)
                {
                    parent = CreateProductHierarchy(connection, catalogueUid, owner, parent_path, product_type);
                }
                hierarchy.ParentUniq = (parent != null) ? parent.Uniq : null;
            }

            connection.Insert(hierarchy);
            return hierarchy;
        }

        public static void AddCatalogueHierarchy(XDatabaseAdapter connection, XDBCatalogueHierarchy hierarchy)
        {
            List<string> path = new List<string>(hierarchy.Path.Split('/'));
            string hierarchy_uniq = CreateHierarchyUniq(connection, path.Count + 6);

            if (path.Count > 1)
            {
                string parent_path = string.Join("/", path.GetRange(0, path.Count - 1).ToArray());
                XDBCatalogueHierarchy parent = connection.Database.Table<XDBCatalogueHierarchy>()
                    .Where(x => x.CatalogueUid == hierarchy.CatalogueUid && x.Path == parent_path && x.ProductType == hierarchy.ProductType)
                    .FirstOrDefault();

                if (parent == null)
                {
                    parent = CreateProductHierarchy(connection, hierarchy.CatalogueUid, hierarchy.MemberUniq, parent_path, hierarchy.ProductType);
                }
                hierarchy.ParentUniq = (parent != null) ? parent.Uniq : null;
            }

            hierarchy.Uniq = hierarchy_uniq;
            hierarchy.Name = path[path.Count - 1];
            hierarchy.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(hierarchy);
        }

        public static bool UpdateCatalogueHierarchy(XDatabaseAdapter connection, XDBCatalogueHierarchy hierarchy, XDBCatalogueHierarchy updated)
        {
            updated.Name = hierarchy.Name;
            updated.Uniq = hierarchy.Uniq;
            updated.MemberUniq = hierarchy.MemberUniq;

            List<string> path = new List<string>(updated.Path.Split('/'));
            string hierarchy_uniq = CreateHierarchyUniq(connection, path.Count + 6);

            if (path.Count > 1)
            {
                string parent_path = string.Join("/", path.GetRange(0, path.Count - 1).ToArray());
                XDBCatalogueHierarchy parent = connection.Database.Table<XDBCatalogueHierarchy>()
                    .Where(x => x.CatalogueUid == updated.CatalogueUid && x.Path == parent_path && x.ProductType == updated.ProductType)
                    .FirstOrDefault();

                if (parent == null)
                {
                    parent = CreateProductHierarchy(connection, updated.CatalogueUid, hierarchy.MemberUniq, parent_path, hierarchy.ProductType);
                }
                updated.ParentUniq = (parent != null) ? parent.Uniq : null;
            }

            if (!updated.CompareTo(hierarchy))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
#endregion

        #region Resources
        public static string CreateResourceUniq(XDatabaseAdapter connection, string prefix)
        {
            string uniq = null;
            bool uniq_ready = false;
            while (!uniq_ready)
            {
                uniq = prefix + XU.GetRandomUniq(12);
                uniq_ready = (connection.Database.Table<XDBCatalogueResource>().Where(x => x.Uniq == uniq).FirstOrDefault() == null);
            }
            return uniq;
        }

        public static void AddResource(XDatabaseAdapter connection, XDBCatalogueResource resource, string prefix)
        {
            resource.Uniq = string.IsNullOrEmpty(resource.Uniq) ? CreateResourceUniq(connection, prefix) : resource.Uniq;
            resource.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(resource);
        }

        public static bool UpdateResource(XDatabaseAdapter connection, XDBCatalogueResource resource, XDBCatalogueResource updated)
        {
            updated.Uniq = resource.Uniq;
            if (!updated.CompareTo(resource))
            {
                updated.CompareTo(resource);
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

        #region Materials
        public static void AddMaterial(XDatabaseAdapter connection, XDBCatalogueMaterial material)
        {
            material.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(material);
        }

        public static bool UpdateMaterial(XDatabaseAdapter connection, XDBCatalogueMaterial material, XDBCatalogueMaterial updated)
        {
            updated.Uniq = material.Uniq;
            if (!updated.CompareTo(material))
            {
                updated.CompareTo(material);
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

        #region Objects
        public static string CreateObjectUniq(XDatabaseAdapter connection, string prefix)
        {
            string uniq = null;
            bool uniq_ready = false;
            while (!uniq_ready)
            {
                uniq = prefix + XU.GetRandomUniq(12);
                uniq_ready = (connection.Database.Table<XDBCatalogueResource>().Where(x => x.Uniq == uniq).FirstOrDefault() == null);
            }
            return uniq;
        }

        public static void AddObject(XDatabaseAdapter connection, XDBCatalogueContent db_object, string prefix)
        {
            db_object.Uniq = CreateObjectUniq(connection, prefix);
            db_object.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(db_object);
        }

        public static bool UpdateObject(XDatabaseAdapter connection, XDBCatalogueContent db_object, XDBCatalogueContent updated)
        {
            updated.Uniq = db_object.Uniq;
            if (!updated.CompareTo(db_object))
            {
                updated.CompareTo(db_object);
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion
        
        #region Products
        public static bool UpdateProduct(XDatabaseAdapter connection, XDBCatalogueProduct product, XDBCatalogueProduct updated)
        {
            updated.Uniq = product.Uniq;
            if (!updated.CompareTo(product))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

        #region Products
        public static string CreateGroupUniq(XDatabaseAdapter connection, string prefix)
        {
            string uniq = null;
            bool uniq_ready = false;
            while (!uniq_ready)
            {
                uniq = prefix + XU.GetRandomUniq(12);
                uniq_ready = (connection.Database.Table<XDBCatalogueGroup>().Where(x => x.Uniq == uniq).FirstOrDefault() == null);
            }
            return uniq;
        }

        public static void AddGroup(XDatabaseAdapter connection, XDBCatalogueGroup product, string prefix)
        {
            string store_uniq = CreateGroupUniq(connection, prefix);
            product.Uniq = store_uniq;
            product.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(product);
        }

        public static bool UpdateGroup(XDatabaseAdapter connection, XDBCatalogueGroup group, XDBCatalogueGroup updated)
        {
            updated.Uniq = group.Uniq;
            if (!updated.CompareTo(group))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion
        
        #region Store
        public static void AddStore(XDatabaseAdapter connection, XDBMemberStore store, string prefix)
        {
            store.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(store);
        }

        public static bool UpdateStore(XDatabaseAdapter connection, XDBMemberStore store, XDBMemberStore updated)
        {
            updated.Uid = store.Uid;
            if (!updated.CompareTo(store))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

        #region ObjectReference
        public static void AddObjectReference(XDatabaseAdapter connection, XDBCatalogueContentReference reference)
        {
            reference.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(reference);
        }

        public static bool UpdateObjectReference(XDatabaseAdapter connection, XDBCatalogueContentReference reference, XDBCatalogueContentReference updated)
        {
            updated.Uid = reference.Uid;
            if (!updated.CompareTo(reference))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

        #region MaterialReference
        public static void AddMaterialReference(XDatabaseAdapter connection, XDBCatalogueMaterialReference reference)
        {
            reference.DateModified = XUtils.GetUnixTimestamp();
            connection.Insert(reference);
        }

        public static bool UpdateMaterialReference(XDatabaseAdapter connection, XDBCatalogueMaterialReference reference, XDBCatalogueMaterialReference updated)
        {
            updated.Uid = reference.Uid;
            if (!updated.CompareTo(reference))
            {
                updated.DateModified = XUtils.GetUnixTimestamp();
                connection.Update(updated);
                return true;
            }
            return false;
        }
        #endregion

#region Access
        /*
        public static List<XDBCatalogueEntry> GetResourcesList(XDatabaseAdapter connection, List<XDBCatalogueHierarchy> hierarchy_list)
        {
            List<XDBCatalogueEntry> entriyList = new List<XDBCatalogueEntry>();
            List<string> productCodes = new List<string>();
            foreach (XDBCatalogueHierarchy hierarchy in hierarchy_list)
            {
                if (!string.IsNullOrEmpty(hierarchy.ProductCode))
                    productCodes.Add(hierarchy.ProductCode);
            }

            if (productCodes.Count == 0)
                return entriyList;

            List<XDBCatalogueProduct> productsList = new List<XDBCatalogueProduct>();
            IEnumerator<XDBCatalogueProduct> it_product = connection.Database.Table<XDBCatalogueProduct>()
                .Where(x => productCodes.Contains(x.ProductCode))
                .GetEnumerator();

            while (it_product.MoveNext())
            {
                XDBCatalogueStore store = connection.Database.Table<XDBCatalogueStore>()
                    .Where(x => x.Uniq == it_product.Current.Uniq)
                    .FirstOrDefault();

                IEnumerator<XDBCatalogueProductReference> it_reference = connection.Database.Table<XDBCatalogueProductReference>()
                    .Where(x => x.ProductUniq == it_product.Current.Uniq)
                    .GetEnumerator();

                while (it_reference.MoveNext())
                {
                    XDBCatalogueMaterial material = connection.Database.Table<XDBCatalogueMaterial>()
                        .Where(x => x.Uniq == it_reference.Current.MaterialUniq)
                        .FirstOrDefault();

                    if (material != null)
                    {
                        XDBCatalogueResource resource = GetCatalogueResource(connection, material.Uniq, 0);

                        entriyList.Add(new XDBCatalogueEntry(
                            it_product.Current,
                            store,
                            hierarchy_list.Find(x => x.ProductCode == it_product.Current.ProductCode),
                            resource
                        ));
                    }
                }
            }
            return entriyList;
        }

        //private static XDBCatalogueResource GetCatalogueResource(SQLiteConnection connection, string mat_uniq, int mat_channel)
        private static XDBCatalogueResource GetCatalogueResource(XDatabaseAdapter connection, string mat_uniq, int mat_channel)
        {
            XDBCatalogueMaterialReference reference = connection.Database.Table<XDBCatalogueMaterialReference>()
                .Where(x => x.MaterialUniq == mat_uniq && x.MaterialChannel == mat_channel)
                .FirstOrDefault();

            if(reference != null)
            {
                if(reference.ReferenceType == XDBCatalogueMaterialReferenceType.MATERIAL)
                {
                    return GetCatalogueResource(connection, reference.ReferenceUniq, mat_channel);
                }
                else
                {
                    return connection.Database.Table<XDBCatalogueResource>().Where(x => x.Uniq == reference.ReferenceUniq).FirstOrDefault();
                }
            }
            return null;
        }
        

    }
}

*/