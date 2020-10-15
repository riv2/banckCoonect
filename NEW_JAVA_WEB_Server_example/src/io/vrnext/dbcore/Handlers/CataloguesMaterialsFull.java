/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore.Handlers;

import com.google.gson.Gson;
import io.vrnext.dbcore.Request.RequestArgument;
import io.vrnext.dbcore.Handlers.IRequestHandler;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.db.C3poDataSource;
import io.vrnext.dbcore.entities.catalogues._catalogues_materials;
import io.vrnext.dbcore.entities.catalogues._catalogues_materials_references;
import io.vrnext.dbcore.entities.catalogues._catalogues_resources;
import java.lang.reflect.Field;
import java.util.HashMap;
import java.util.List;
import org.db.JDBCCloser;
import org.db.JDBCMapper;

/**
 *
 * @author riv
 */
public class CataloguesMaterialsFull implements IRequestHandler {

    private static final Logger logger = LogManager.getLogger(CataloguesMaterialsFull.class.getName());

    // Аргументом должен служить RequestContext
    @Override
    public HashMap GET(String args) {

        // Эти данные должны быть помещены в RequestContext
        String argsKey             = "";
        String argsValue           = "";
        String queryWhere          = " 1 = 1 ";
        String queryLimit          = " LIMIT ";
        String queryOffset         = " OFFSET ";
        String queryOrder          = " ORDER BY ";
        String queryOrderDirection = " ASC ";
        String querySeporator      = " AND ";
        String querySeporatorDef   = " AND ";
        String querySeporatorBuff  = " AND ";
        String queryLimitValue     = "100";
        String queryOffsetValue    = "0";
        String queryFieldsValue    = "*";
        String queryOrderValue     = "1";

        Gson gson = new Gson();
        JDBCMapper maper = new JDBCMapper();
        HashMap<String, Object> responceEntities = new HashMap();
        RequestArgument[] params = gson.fromJson(args, RequestArgument[].class);

        // Requests
        for (int i = 0; i < params.length; i++) {
            if (params[i].key.toUpperCase().equals("SEPORATOR")) {
                querySeporatorBuff = " " + params[i].value[0] + " ";
            }
        }

        for (int i = 0; i < params.length; i++) {
            argsValue = "";
            argsKey = params[i].key.replace("[]", "");

            if (i == 0) {
                querySeporator = querySeporatorDef;
            } else {
                querySeporator = querySeporatorBuff;
            }

            switch (argsKey.toUpperCase()) {
                case ("LIMIT"):
                    queryLimitValue = params[i].value[0];
                    break;
                case ("OFFSET"):
                    queryOffsetValue = params[i].value[0];
                    break;
                case ("FIELDS"):
                    queryFieldsValue = params[i].value[0];
                    break;                
                case ("ORDER"): 
                    queryOrderValue = params[i].value[0];
                    break;
                case ("DESC"): 
                    queryOrderDirection = " DESC ";
                    break;
                    
                default:
                    if (params[i].value.length > 1) {
                        for (int n = 0; n < params[i].value.length; n++) {
                            argsValue = argsValue + "'" + params[i].value[n] + "',";
                        }
                        argsValue = argsValue.substring(0, argsValue.length() - 1);
                        if (argsValue.length() == 0) {
                            argsValue = "''";
                        }
                        for (Field field : _catalogues_materials.class.getDeclaredFields()) {
                            if (field.getName().equals(argsKey)) {
                                queryWhere = queryWhere + querySeporator + argsKey + " in (" + argsValue + ") ";
                            }
                        }
                    } else {
                        for (Field field : _catalogues_materials.class.getDeclaredFields()) {
                            if (field.getName().equals(argsKey)) {
                                queryWhere = queryWhere + querySeporator + argsKey + " = '" + params[i].value[0] + "' ";
                            }
                        }
                    }
            }
        }

        queryLimit  = queryLimit  + queryLimitValue;
        queryOffset = queryOffset + queryOffsetValue;
        queryOrder  = queryOrder  + queryOrderValue + queryOrderDirection;
        // Requests end

        // Response
        try {
            // Подключение к базе должно запрашиваться из RequestContext
            Connection c = C3poDataSource.getConnection();

            PreparedStatement ps_catalogues_materials = c.prepareStatement("SELECT " + queryFieldsValue + " FROM _catalogues_materials WHERE " + queryWhere + queryOrder + queryLimit + queryOffset, ResultSet.TYPE_SCROLL_INSENSITIVE, ResultSet.CONCUR_READ_ONLY);
            PreparedStatement ps_catalogues_materials_references = c.prepareStatement("SELECT " + queryFieldsValue + " FROM _catalogues_materials_references WHERE material_uniq in (SELECT uniq FROM _catalogues_materials WHERE " + queryWhere + queryOrder + queryLimit + queryOffset + ")", ResultSet.TYPE_SCROLL_INSENSITIVE, ResultSet.CONCUR_READ_ONLY);
            PreparedStatement ps_catalogues_resources = c.prepareStatement("SELECT " + queryFieldsValue + " FROM _catalogues_resources WHERE uniq in (SELECT reference_uniq FROM _catalogues_materials_references WHERE material_uniq in (SELECT uniq FROM _catalogues_materials WHERE " + queryWhere + queryOrder + queryLimit + queryOffset + "))", ResultSet.TYPE_SCROLL_INSENSITIVE, ResultSet.CONCUR_READ_ONLY);

            logger.debug("SELECT " + queryFieldsValue + " FROM _catalogues_materials WHERE " + queryWhere + queryOrder + queryLimit + queryOffset);

            ResultSet rs_catalogues_materials = ps_catalogues_materials.executeQuery();
            ResultSet rs_catalogues_materials_references = ps_catalogues_materials_references.executeQuery();
            ResultSet rs_catalogues_resources = ps_catalogues_resources.executeQuery();

            List<_catalogues_materials> _catalogues_materials_entities = maper.mapRersultSetToObject(rs_catalogues_materials, _catalogues_materials.class);
            List<_catalogues_materials_references> _catalogues_materials_references_entities = maper.mapRersultSetToObject(rs_catalogues_materials_references, _catalogues_materials_references.class);
            List<_catalogues_resources> _catalogues_resources_entities = maper.mapRersultSetToObject(rs_catalogues_resources, _catalogues_resources.class);

            responceEntities.put("_catalogues_materials", _catalogues_materials_entities);
            responceEntities.put("_catalogues_materials_references", _catalogues_materials_references_entities);
            responceEntities.put("_catalogues_resources", _catalogues_resources_entities);

            JDBCCloser.close_trough(rs_catalogues_materials);
            JDBCCloser.close_trough(rs_catalogues_materials_references);
            JDBCCloser.close_trough(rs_catalogues_resources);

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

}
