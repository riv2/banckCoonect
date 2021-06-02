/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.db;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/**
 *
 * @author Radmont
 */
public class JDBCCloser {
    public static void close_trough(ResultSet... resultSets) 
    {
        if (resultSets == null)
            return;

        for (ResultSet resultSet : resultSets)
        {
            if (resultSet != null)
            {
                try {
                    Statement statement = resultSet.getStatement();
                    Connection connection = statement.getConnection();
                    resultSet.close();
                    statement.close();
                    connection.close();
                } catch (SQLException e) {
                    // Do some exception-logging here.
                }
            }
        }
    }
}
