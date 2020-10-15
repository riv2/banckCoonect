package io.vrnext.dbcore.Handlers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
import io.vrnext.dbcore.entities.companies._companies_store;

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
import java.util.Scanner;
import org.db.JDBCCloser;
import org.db.JDBCMapper;
import java.util.List;

/**
 *
 * @author korni
 */
public class CompaniesStoreByProductUniq implements IRequestHandler { 
    
    private static final Logger logger = LogManager.getLogger(CompaniesStoreByProductUniq.class.getName());

    @Override
    public HashMap GET(String args) {      

        String mainQuery     = "";
        String searchQuery   = "";
        Class classEntity    = _companies_store.class;
        String companyUid    = "";
        String argsKey       = "";
        String productUniqs  = "";

        Gson gson = new Gson();
        JDBCMapper maper = new JDBCMapper();
        HashMap<String, Object> responceEntities = new HashMap();
        RequestArgument[] params = gson.fromJson(args, RequestArgument[].class);
        
        // Requests
        for (int i = 0; i < params.length; i++) { 
            argsKey = params[i].key.replace("[]", "").toUpperCase(); 
 
            if (argsKey.equals("COMPANY_UID")) {
               companyUid = params[i].value[0];
            }             
            if (argsKey.equals("PRODUCT_UNIQ")){           
                if (params[i].value.length > 1) {
                    for (int n = 0; n < params[i].value.length; n++) {  
                         productUniqs = productUniqs + "'" + params[i].value[n] + "',";                          
                    }               
                } else {
                     productUniqs = productUniqs + "'" + params[i].value[0] + "'";  
                } 
            }                    
        }

        productUniqs = productUniqs.substring(0, productUniqs.length() - 1);
        if (productUniqs.length() == 0) {
            productUniqs = "''";
        }  

        mainQuery = "SELECT *, cpg.product_uniq AS product_uniq FROM _companies_store cs " +
                    "LEFT JOIN _catalogues_products_groups cpg ON cs.group_uniq=cpg.group_uniq " +
                    "WHERE cs.group_uniq in ( " +
                    "SELECT group_uniq FROM _catalogues_products_groups WHERE product_uniq in " +
                    "(%s)) and company_uid = '%s'; ";

        // Requests end
       
          // Response
        try { 
            // Подключение к базе должно запрашиваться из RequestContext
            Connection c = C3poDataSource.getConnection();                         
            Statement  s = c.createStatement();
 
            mainQuery = mainQuery.format(mainQuery, productUniqs, companyUid);
         
            ResultSet rs_table = s.executeQuery(mainQuery);
            
            List<?> _entities = maper.mapRersultSetToObject(rs_table, classEntity);            
           
            responceEntities.put("_companies_store", _entities);  

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
         Scanner sc = new Scanner(search);
              while (sc.hasNext()) {
                if (sc.hasNextInt()) {
                      int num = sc.nextInt();
                      return num;
                }
            }              
        return -1; 
    }
    
}
