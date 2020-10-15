/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.db;

import java.lang.reflect.Field;
import java.lang.reflect.InvocationTargetException;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.sql.Types;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;

import javax.persistence.Column;
import javax.persistence.Entity;
import org.apache.commons.beanutils.BeanUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class JDBCMapper<T> {

    @SuppressWarnings("unchecked")
    public List<T> mapRersultSetToObject(ResultSet rs, Class outputClass) {
        List<T> outputList = new ArrayList<>();
        try {
            // make sure resultset is not null
            if (rs != null) {
                // check if outputClass has 'Entity' annotation
                if (outputClass.isAnnotationPresent(Entity.class)) {
                    // get the resultset metadata
                    ResultSetMetaData rsmd = rs.getMetaData();
                    // get all the attributes of outputClass
                    Field[] fields = outputClass.getDeclaredFields();
                    while (rs.next()) {
                        T bean = (T) outputClass.newInstance();
                        for (int _iterator = 0; _iterator < rsmd
                                .getColumnCount(); _iterator++) {
                            // getting the SQL column name
                            String columnName = rsmd
                                    .getColumnName(_iterator + 1);
                            // reading the value of the SQL column
                            Object columnValue = rs.getObject(_iterator + 1);
                            // iterating over outputClass attributes to check if any attribute has 'Column' annotation with matching 'name' value
                            for (Field field : fields) {
                                if (field.isAnnotationPresent(Column.class)) {
                                    Column column = field
                                            .getAnnotation(Column.class);
                                    if (column.name().equalsIgnoreCase(
                                            columnName)
                                            && columnValue != null) {
                                        BeanUtils.setProperty(bean, field
                                                .getName(), columnValue);
                                        break;
                                    }
                                }
                            }
                        }
                        outputList.add(bean);
                    }

                } else {
                    // throw some error
                }
            } else {
                return outputList;
            }
        } catch (IllegalAccessException | SQLException | InstantiationException | InvocationTargetException ex) {
            Logger.getLogger(JDBCMapper.class.getName()).log(Level.SEVERE, null, ex);        
        }
        finally {
            //JDBCCloser.close_trough(rs);
        }
        return outputList;
    }
    
    public HashMap<String, Object> mapObjectToRequest(T input, Class inputClass) {
        HashMap<String, Object> result = new HashMap<>();
        try {
            // check if inputClass has 'Entity' annotation
            if (inputClass.isAnnotationPresent(Entity.class)) {
                // get all the attributes of inputClass
                Field[] fields = inputClass.getDeclaredFields();
                T bean = (T) inputClass.newInstance();
                for (Field field : fields) {
                    if (field.isAnnotationPresent(Column.class)) {
                        Column column = field.getAnnotation(Column.class);
                        result.put(column.name(), BeanUtils.getProperty(bean, field.getName()));
                    }
                }
            }
        } catch (IllegalAccessException | InstantiationException | InvocationTargetException | NoSuchMethodException ex) {
            Logger.getLogger(JDBCMapper.class.getName()).log(Level.SEVERE, null, ex);        
        }
        return result;
    }

    public static JSONArray mapResultSetToJSON(ResultSet rs){
        JSONArray jsonList = new JSONArray();
        try {
            if (rs.first()) {
                ResultSetMetaData rsMetaData = rs.getMetaData();
                int numberOfColumns = rsMetaData.getColumnCount();

                do {
                    JSONObject jsonData = new JSONObject();

                    for (int i = 1; i <= numberOfColumns; i++) {
                        String fieldName = rsMetaData.getColumnLabel(i);

                        switch (rsMetaData.getColumnType(i)) {
                            case Types.INTEGER:
                                jsonData.put(fieldName, rs.getInt(i));
                                break;

                            case Types.DOUBLE:
                                jsonData.put(fieldName, rs.getDouble(i));
                                break;

                            case Types.FLOAT:
                                jsonData.put(fieldName, rs.getFloat(i));
                                break;

                            case Types.BOOLEAN:
                                jsonData.put(fieldName, rs.getBoolean(i));
                                break;

                            case Types.TIMESTAMP:
                                jsonData.put(fieldName, rs.getTimestamp(i));
                                break;

                            case Types.NVARCHAR:
                            case Types.VARCHAR:
                                jsonData.put(fieldName, rs.getString(i));
                                break;

                            default:
                                jsonData.put(fieldName, rs.getString(i));
                                break;
                        }
                    }

                    jsonList.put(jsonData);
                } while (rs.next());
            }
        } catch (SQLException | JSONException ex) {
            Logger.getLogger(JDBCMapper.class.getName()).log(Level.SEVERE, null, ex);
        } finally {
            //JDBCCloser.close_trough(rs);
        }
        return jsonList;
    }
}
