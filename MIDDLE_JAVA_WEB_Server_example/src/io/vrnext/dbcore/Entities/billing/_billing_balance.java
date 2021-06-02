package io.vrnext.dbcore.entities.billing;

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
@Table(name = "_billing_balance")
public class _billing_balance implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "uniq_member")
    private String uniq_member;
    
    @Column(name = "uniq_company")
    private String uniq_company;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getUniq_member() {
        return uniq_member;
    }

    public void setUniq_member(String uniq_member) {
        this.uniq_member = uniq_member;
    }

    public String getUniq_company() {
        return uniq_company;
    }

    public void setUniq_company(String uniq_company) {
        this.uniq_company = uniq_company;
    }

    public float getBalance() {
        return balance;
    }

    public void setBalance(float balance) {
        this.balance = balance;
    }

    public String getDate_updated() {
        return date_updated;
    }

    public void setDate_updated(String date_updated) {
        this.date_updated = date_updated;
    }
    
    @Column(name = "balance")
    private float balance;
    
    @Column(name = "date_updated")
    private String date_updated;

    
}
