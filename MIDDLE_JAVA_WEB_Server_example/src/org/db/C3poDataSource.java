/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.db;

import com.mchange.v2.c3p0.ComboPooledDataSource;
import java.beans.PropertyVetoException;
import java.sql.Connection;
import java.sql.SQLException;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

/**
 *
 * @author Leonid Gudkov
 */
public class C3poDataSource {
    private static final Logger logger = LogManager.getLogger(C3poDataSource.class.getName());
    private static final ComboPooledDataSource cpds = new ComboPooledDataSource();
    
    public static final String URL = "jdbc:postgresql://localhost:5432/?databaseName=;sendStringParametersAsUnicode=false;prepareSQL=1;";
   
    public static final String USER = "";
    public static final String PASSWORD = "";
 
    static {
        try {
            cpds.setDriverClass("org.postgresql.Driver"); 
            cpds.setJdbcUrl(URL);
            cpds.setUser(USER);
            cpds.setPassword(PASSWORD);     
            cpds.setMaxPoolSize(100);  
        } catch (PropertyVetoException ex) {
             logger.error("error message: " + ex.getMessage());
        }
    }
     
    public static Connection getConnection() throws SQLException {
        return cpds.getConnection();
    }
     
    private C3poDataSource(){}
}