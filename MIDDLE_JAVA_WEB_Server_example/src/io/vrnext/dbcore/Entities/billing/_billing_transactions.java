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
@Table(name = "_billing_transactions")
public class _billing_transactions implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "uniq_member")
    private String uniq_member;
    
    @Column(name = "uid_product")
    private String uid_product;
    
    @Column(name = "type_transaction")
    private String type_transaction;
    
    @Column(name = "hide_transaction")
    private String hide_transaction;
    
    @Column(name = "sum")
    private float sum;
    
    @Column(name = "product_serialize")
    private String product_serialize;
    
    @Column(name = "signature")
    private String signature;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date")
    private String date;

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

    public String getUid_product() {
        return uid_product;
    }

    public void setUid_product(String uid_product) {
        this.uid_product = uid_product;
    }

    public String getType_transaction() {
        return type_transaction;
    }

    public void setType_transaction(String type_transaction) {
        this.type_transaction = type_transaction;
    }

    public String getHide_transaction() {
        return hide_transaction;
    }

    public void setHide_transaction(String hide_transaction) {
        this.hide_transaction = hide_transaction;
    }

    public float getSum() {
        return sum;
    }

    public void setSum(float sum) {
        this.sum = sum;
    }

    public String getProduct_serialize() {
        return product_serialize;
    }

    public void setProduct_serialize(String product_serialize) {
        this.product_serialize = product_serialize;
    }

    public String getSignature() {
        return signature;
    }

    public void setSignature(String signature) {
        this.signature = signature;
    }

    public String getDate_created() {
        return date_created;
    }

    public void setDate_created(String date_created) {
        this.date_created = date_created;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    
    
    
}
