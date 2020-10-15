/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package io.vrnext.dbcore.Handlers;
import java.util.HashMap;

/**
 *
 * @author korni
 */
public class RequestHandlerEmpty implements IRequestHandler {
    
    @Override public HashMap GET(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap(); 
        return responceEntities;        
    }    
    
    @Override public HashMap PUT(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap(); 
        return responceEntities;             
    }
    
    @Override public HashMap POST(String parametrs) {
        HashMap<String, Object> responceEntities = new HashMap(); 
        return responceEntities;             
    }
    
    @Override public HashMap DELETE(String parametrs) {
         HashMap<String, Object> responceEntities = new HashMap(); 
        return responceEntities;        
    }
}
