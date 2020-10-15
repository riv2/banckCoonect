/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore.Request;

/**
 *
 * @author korni
 */
public class RequestArgument {

    public String key;
    
    public String[] value;

    public RequestArgument(String key, String[] value) {
        this.key = key;
        this.value = value;
    }

}
