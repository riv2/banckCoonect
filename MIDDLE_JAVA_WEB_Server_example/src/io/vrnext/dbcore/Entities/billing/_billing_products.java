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
@Table(name = "_billing_products")
public class _billing_products implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "name")
    private String name;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date_updated")
    private String date_updated;
    
    @Column(name = "article")
    private String article;
    
    @Column(name = "advanced")
    private String advanced;
    
    @Column(name = "type_product")
    private String type_product;
    
    @Column(name = "table")
    private String table;
    
    @Column(name = "uniq_table")
    private String uniq_table;
    
    @Column(name = "status")
    private String status;
    
    @Column(name = "code")
    private String code;
    
    @Column(name = "advanced_value")
    private String advanced_value;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
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

    public String getAdvanced() {
        return advanced;
    }

    public void setAdvanced(String advanced) {
        this.advanced = advanced;
    }

    public String getType_product() {
        return type_product;
    }

    public void setType_product(String type_product) {
        this.type_product = type_product;
    }

    public String getTable() {
        return table;
    }

    public void setTable(String table) {
        this.table = table;
    }

    public String getUniq_table() {
        return uniq_table;
    }

    public void setUniq_table(String uniq_table) {
        this.uniq_table = uniq_table;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
    }

    public String getAdvanced_value() {
        return advanced_value;
    }

    public void setAdvanced_value(String advanced_value) {
        this.advanced_value = advanced_value;
    }
    
    
    
}
