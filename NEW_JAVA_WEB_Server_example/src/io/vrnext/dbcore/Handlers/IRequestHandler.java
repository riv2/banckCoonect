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
public interface IRequestHandler {

    public HashMap GET(String args);

    public HashMap PUT(String parametrs);

    public HashMap POST(String parametrs);

    public HashMap DELETE(String parametrs);
}
