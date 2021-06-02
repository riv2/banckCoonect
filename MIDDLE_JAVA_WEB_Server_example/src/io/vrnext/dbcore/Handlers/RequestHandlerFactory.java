/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore.Handlers;

/**
 *
 * @author korni
 */
public class RequestHandlerFactory {
  
    public Object getModel(String classname) throws ClassNotFoundException, InstantiationException, IllegalAccessException { 
        return Class.forName(classname).newInstance();
    } 
    
}
