package io.vrnext.dbcore.Handlers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
import io.vrnext.dbcore.entities.catalogues._catalogues_products;

import com.google.gson.Gson;
import io.vrnext.dbcore.Request.RequestArgument;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.db.C3poDataSource;
import java.sql.Statement;
import java.util.HashMap;
import java.util.List;
import java.util.Scanner;
import java.util.logging.Level;
import org.db.JDBCCloser;
import org.db.JDBCMapper;

/**
 *
 * @author korni
 */
public class ProductSearch implements IRequestHandler { 
    
    private static final Logger logger = LogManager.getLogger(ProductSearch.class.getName());

    @Override
    public HashMap GET(String args) {      
        
        String search        = "";
        String mainQuery     = "";
        String searchQuery   = "";
        Class classEntity    = _catalogues_products.class;
        Integer width        = 0;
        Integer height       = 0;
        Integer size         = -1;
        String catalogue_uid = "2";
        String product_type  = "2";
        ResultSet rs_table   = null;
        Connection c         = null;
        Statement s          = null;    

        Gson gson = new Gson();
        JDBCMapper maper = new JDBCMapper();
        HashMap<String, Object> responceEntities = new HashMap();
        RequestArgument[] params = gson.fromJson(args, RequestArgument[].class);

        // Requests
        for (int i = 0; i < params.length; i++) {
            if (params[i].key.toUpperCase().equals("SEARCH")) {
                search = params[i].value[0];
            }  
            if (params[i].key.toUpperCase().equals("CATALOGUE")) {
                catalogue_uid = params[i].value[0];
            }          
        }        
     
        String[] searchWords = search.split(" ");
        
        if (searchWords.length == 1) {
            size = ProductSearch.TryParse(searchWords[0]);
            width = size;
            height = size;
        } else if (searchWords.length == 2)  {
            size = ProductSearch.TryParse(searchWords[0]);
            width = size;            
            size = ProductSearch.TryParse(searchWords[1]);
            height = size;
        } else if (ProductSearch.TryParse(searchWords[1]) != -1) {
            size = ProductSearch.TryParse(searchWords[1]);
            width = size;
            height = size;            
        }        

        mainQuery = "SELECT prd.*, pr.checksum, prdst.article, prdst.available, prdst.price, prdst.units, prdst.company_uid FROM (SELECT * FROM _catalogues_products WHERE uniq IN " +
            "(SELECT product_uniq FROM _catalogues_products_groups WHERE group_uniq IN " +
            "(SELECT uniq FROM _catalogues_groups WHERE hierarchy_uniq IN " +
            "(SELECT uniq FROM _catalogues_hierarchy WHERE catalogue_uid='%s' AND product_type='%s')))) AS prd" +
            " LEFT JOIN _catalogues_materials_references cmr ON cmr.material_uniq=prd.uniq" +
            " LEFT JOIN _catalogues_resources pr ON pr.uniq=cmr.reference_uniq" +
            " LEFT JOIN _catalogues_products_groups prdgr ON prdgr.product_uniq=prd.uniq" +
            " LEFT JOIN _companies_store prdst ON prdst.group_uniq=prdgr.group_uniq" +
            " LEFT JOIN _catalogues_groups grp ON grp.uniq=prdgr.group_uniq" +
            " LEFT JOIN _catalogues_hierarchy prdhr ON prdhr.uniq=grp.hierarchy_uniq";

        if(width > 0 || height > 0) {
            mainQuery += " WHERE prd.uniq LIKE '%s' OR prd.name LIKE '%s' OR prdhr.path LIKE '%s' OR (prdst.article IS NOT NULL AND prdst.article LIKE '%s') OR prd.dim_x='%s' OR prd.dim_y='%s' LIMIT 100";
        } else {
            mainQuery += " WHERE prd.uniq LIKE '%s' OR prd.name LIKE '%s' OR prdhr.path LIKE '%s' OR (prdst.article IS NOT NULL AND prdst.article LIKE '%s') LIMIT 100; /*$s $s */";
        }     
        // Requests end
       
          // Response
        try { 
            // Подключение к базе должно запрашиваться из RequestContext
            c = C3poDataSource.getConnection();                         
            s = c.createStatement();
            
            mainQuery = mainQuery.format(mainQuery, catalogue_uid, product_type, search, search, search, search, width, height);

            rs_table = s.executeQuery(mainQuery);

            List<?> _entities = maper.mapRersultSetToObject(rs_table, classEntity);            
           
            responceEntities.put("_products", _entities);   
            
            JDBCCloser.close_trough(rs_table);          
        
        } catch (SQLException ex) {
            
            logger.error("error message: " + ex.getMessage());
            
        } 
        // Response end

        return responceEntities;

    }    
    
    @Override
    public HashMap PUT(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }

    @Override
    public HashMap POST(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }

    @Override
    public HashMap DELETE(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap();
        return responceEntities;
    }     
    
    static private int TryParse(String search) {        
        try {
            return Integer.parseInt(search);
        } catch (NumberFormatException e) {
            return -1;
        }
    }
    
}
