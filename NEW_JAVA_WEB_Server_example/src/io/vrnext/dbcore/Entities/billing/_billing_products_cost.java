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
@Table(name = "_billing_products_cost")
public class _billing_products_cost implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "uid_product")
    private String uid_product;
    
    @Column(name = "uniq_currency")
    private String uniq_currency;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date_updated")
    private String date_updated;
    
    @Column(name = "article")
    private String article;
    
    @Column(name = "cost")
    private float cost;
    
    @Column(name = "count")
    private String count;
    
    @Column(name = "advanced")
    private String advanced;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getUid_product() {
        return uid_product;
    }

    public void setUid_product(String uid_product) {
        this.uid_product = uid_product;
    }

    public String getUniq_currency() {
        return uniq_currency;
    }

    public void setUniq_currency(String uniq_currency) {
        this.uniq_currency = uniq_currency;
    }

    public String getDate_created() {
        return date_created;
    }

    public void setDate_created(String date_created) {
        this.date_created = date_created;
    }

    public String getDate_updated() {
        return date_updated;
    }

    public void setDate_updated(String date_updated) {
        this.date_updated = date_updated;
    }

    public String getArticle() {
        return article;
    }

    public void setArticle(String article) {
        this.article = article;
    }

    public float getCost() {
        return cost;
    }

    public void setCost(float cost) {
        this.cost = cost;
    }

    public String getCount() {
        return count;
    }

    public void setCount(String count) {
        this.count = count;
    }

    public String getAdvanced() {
        return advanced;
    }

    public void setAdvanced(String advanced) {
        this.advanced = advanced;
    }
    
 
    
    
    
}
