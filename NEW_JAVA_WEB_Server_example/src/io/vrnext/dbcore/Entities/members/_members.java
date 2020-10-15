package io.vrnext.dbcore.entities.members;

import java.io.Serializable;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author riv
 */

@Entity
@Table(name = "_members")
public class _members implements Serializable {
       
    @Id
    @Column(name = "uniq")
    private String  uniq;    
     
    @Column(name = "email")
    private String email;
    
    @Column(name = "password_salt")
    private String password_salt;
    
    @Column(name = "password")
    private String password;
    
    @Column(name = "access_group")
    private Integer access_group;
    
    @Column(name = "activation_key")
    private String activation_key;
    
    @Column(name = "date_activate")
    private Integer date_activate;
    
    @Column(name = "currency_uniq")
    private String currency_uniq;

    public String getUniq() {
        return uniq;
    }

    public void setUniq(String uniq) {
        this.uniq = uniq;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPassword_salt() {
        return password_salt;
    }

    public void setPassword_salt(String password_salt) {
        this.password_salt = password_salt;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public Integer getAccess_group() {
        return access_group;
    }

    public void setAccess_group(Integer access_group) {
        this.access_group = access_group;
    }

    public String getActivation_key() {
        return activation_key;
    }

    public void setActivation_key(String activation_key) {
        this.activation_key = activation_key;
    }

    public Integer getDate_activate() {
        return date_activate;
    }

    public void setDate_activate(Integer date_activate) {
        this.date_activate = date_activate;
    }

    public String getCurrency_uniq() {
        return currency_uniq;
    }

    public void setCurrency_uniq(String currency_uniq) {
        this.currency_uniq = currency_uniq;
    }

}
