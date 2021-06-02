package io.vrnext.dbcore.Handlers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

import io.vrnext.dbcore.entities.catalogues.*;
import io.vrnext.dbcore.entities.billing.*;
import io.vrnext.dbcore.entities.members.*;
import io.vrnext.dbcore.entities.companies.*;
import io.vrnext.dbcore.entities.other.*;

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
import java.lang.reflect.Field;
import java.sql.Statement;
import java.util.HashMap;
import java.util.List;
import org.db.JDBCCloser;
import org.db.JDBCMapper;
import org.postgresql.util.GettableHashMap;

/**
 *
 * @author korni
 */
public class RequestHandlerByOneTable implements IRequestHandler { 
    
    private static final Logger logger = LogManager.getLogger(RequestHandlerByOneTable.class.getName());

    @Override
    public HashMap GET(String args) {
        
         // Эти данные должны быть помещены в RequestContext
        String argsKey             = "";
        String argsValue           = "";
        String argValue            = "";
        String queryWhere          = " 1 = 1 ";
        String queryLimit          = " LIMIT ";        
        String queryOffset         = " OFFSET ";
        String queryOrder          = " ORDER BY ";
        String queryGroup          = "";
        String queryOrderDirection = " ASC ";
        String querySeporator      = " AND ";
        String querySeporatorDef   = " AND ";
        String querySeporatorBuff  = " AND ";
        String queryLimitValue     = "100";
        String queryOffsetValue    = "0"; 
        String queryFieldsValue    = "*";
        String queryOrderValue     = "1";
        String table               = "";
        String equality            = " = ";
        String equalityIn          = " IN ";
        Class classEntity          = _void.class;
        String mainQuery           = "";
        
        HashMap<String, Class> tables;        
        tables = new GettableHashMap<>();
        
        tables.put("_catalogues",            _catalogues.class);
        tables.put("_catalogues_groups",     _catalogues_groups.class);  
        tables.put("_catalogues_hierarchy",  _catalogues_hierarchy.class);  
        tables.put("_catalogues_materials",  _catalogues_materials.class); 
        tables.put("_catalogues_products",   _catalogues_products.class); 
        tables.put("_catalogues_properties", _catalogues_properties.class);  
        tables.put("_catalogues_resources",  _catalogues_resources.class);        
        tables.put("_companies",             _companies.class);  
        tables.put("_companies_branches",    _companies_branches.class);  
        tables.put("_companies_catalogues",  _companies_catalogues.class);  
        tables.put("_companies_members",     _companies_members.class);  
        tables.put("_companies_store",       _companies_store.class);  
        tables.put("_contact_info",          _contact_info.class);  
        tables.put("_members",               _members.class);        
        tables.put("_members_catalogues",    _members_catalogues.class);  
        tables.put("_members_devices",       _members_devices.class);  
        tables.put("_members_profiles",      _members_profiles.class);  
        tables.put("_members_sessions",      _members_sessions.class);  
        tables.put("_members_settings",      _members_settings.class);
        tables.put("_billing_balance",       _billing_balance.class);        
        tables.put("_billing_currences",     _billing_currences.class);  
        tables.put("_billing_discounts",     _billing_discounts.class);  
        tables.put("_billing_gateways",      _billing_gateways.class);  
        tables.put("_billing_logs",          _billing_logs.class);  
        tables.put("_billing_products",      _billing_products.class);
        tables.put("_billing_products_cost", _billing_products_cost.class);
        tables.put("_billing_transactions",  _billing_transactions.class);
        tables.put("_void",                  _void.class);
        tables.put("_catalogues_materials_references", _catalogues_materials_references.class);  
        tables.put("_catalogues_products_groups",      _catalogues_products_groups.class);  

        Gson gson = new Gson();
        JDBCMapper maper = new JDBCMapper();
        HashMap<String, Object> responceEntities = new HashMap();
        RequestArgument[] params = gson.fromJson(args, RequestArgument[].class);
        
        // Requests
        for (int i = 0; i < params.length; i++) {
            if (params[i].key.toUpperCase().equals("SEPORATOR")) {
                querySeporatorBuff = " " + params[i].value[0] + " ";
            }
            if (params[i].key.toUpperCase().equals("TABLENAME")) {
                if(tables.containsKey(params[i].value[0])) {
                    classEntity = tables.get(params[i].value[0]);
                    table       = params[i].value[0];
                } else {
                    return responceEntities;
                }
            }
        }
        
        for (int i = 0; i < params.length; i++) {
        argsValue = "";
        argValue  = "";
        argsKey   = params[i].key.replace("[]", ""); 
        equality  = " = "; 
        equalityIn = " IN ";
        
        int indexNot  = params[i].key.indexOf("!");
        int indexMore = params[i].key.indexOf(">");
        int indexLess = params[i].key.indexOf("<"); 
        
        if(indexNot  > - 1) { argsKey = argsKey.replace("!", ""); equality   = " <> "; equalityIn = " NOT IN "; }
        if(indexMore > - 1) { argsKey = argsKey.replace(">", ""); equality   = " > "; }
        if(indexLess > - 1) { argsKey = argsKey.replace("<", ""); equality   = " < "; }

        if (i == 0) { querySeporator = querySeporatorDef; } else { querySeporator = querySeporatorBuff; }
        
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
            case ("GROUP"): 
                queryGroup = " GROUP BY " + params[i].value[0];
                break;                 
                
            default:
                if (params[i].value.length > 1) {
                    for (int n = 0; n < params[i].value.length; n++) {                           
                        int indexNull = params[i].value[n].indexOf("null");
                        if(indexNull == - 1) { 
                            argsValue = argsValue + "'" + params[i].value[n] + "',";                             
                        } else {
                            argsValue = argsValue + "" + params[i].value[n] + ",";
                        }  
                    }
                    argsValue = argsValue.substring(0, argsValue.length() - 1);
                    if (argsValue.length() == 0) {
                        argsValue = "''";
                    }

                    for (Field field : classEntity.getDeclaredFields()) {
                        if (field.getName().equals(argsKey)) {
                            queryWhere = queryWhere + querySeporator + argsKey + " " + equalityIn + " (" + argsValue + ") ";
                        }
                    }
                } else {
                    for (Field field : classEntity.getDeclaredFields()) {
                        if (field.getName().equals(argsKey)) {                            
                            int indexNull = params[i].value[0].indexOf("null");
                            if(indexNull == - 1) { 
                                argValue = "'" + params[i].value[0] + "'";                             
                            } else {
                                equality = " IS ";
                                argValue = params[i].value[0];  
                            } 
                            queryWhere = queryWhere + querySeporator + argsKey + equality + argValue;
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
            Statement  s = c.createStatement();
            
            mainQuery = mainQuery.format("SELECT %s FROM %s WHERE %s %s %s %s %s", queryFieldsValue, table, queryWhere, queryGroup, queryOrder, queryLimit, queryOffset);

            ResultSet rs_table = s.executeQuery(mainQuery);
            
            List<?> _entities = maper.mapRersultSetToObject(rs_table, classEntity);            
           
            responceEntities.put(table, _entities);  

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
    
}
