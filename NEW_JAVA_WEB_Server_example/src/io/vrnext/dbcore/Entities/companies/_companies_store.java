package io.vrnext.dbcore.entities.companies;

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
@Table(name = "_companies_store")
public class _companies_store implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "company_uid")
    private Integer company_uid;
    
    @Column(name = "group_uniq")
    private String group_uniq;
    
    @Column(name = "article")
    private String article;
    
    @Column(name = "currency")
    private String currency;
    
    @Column(name = "calculation")
    private Integer calculation;
    
    @Column(name = "units")
    private Integer units;
    
    @Column(name = "price")
    private float price;
    
    @Column(name = "available")
    private float available;
    
    @Column(name = "date_modified")
    private Integer date_modified;
    
    @Column(name = "product_uniq")
    private String  product_uniq;    
    
    public String getProduct_uniq() {
        return product_uniq;
    }
    public void setProduct_uniq(String product_uniq) {
        this.product_uniq = product_uniq;
    }

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public Integer getCompany_uid() {
        return company_uid;
    }

    public void setCompany_uid(Integer company_uid) {
        this.company_uid = company_uid;
    }

    public String getGroup_uniq() {
        return group_uniq;
    }

    public void setGroup_uniq(String group_uniq) {
        this.group_uniq = group_uniq;
    }

    public String getArticle() {
        return article;
    }

    public void setArticle(String article) {
        this.article = article;
    }

    public String getCurrency() {
        return currency;
    }

    public void setCurrency(String currency) {
        this.currency = currency;
    }

    public Integer getCalculation() {
        return calculation;
    }

    public void setCalculation(Integer calculation) {
        this.calculation = calculation;
    }

    public Integer getUnits() {
        return units;
    }

    public void setUnits(Integer units) {
        this.units = units;
    }

    public float getPrice() {
        return price;
    }

    public void setPrice(float price) {
        this.price = price;
    }

    public float getAvailable() {
        return available;
    }

    public void setAvailable(float available) {
        this.available = available;
    }

    public Integer getDate_modified() {
        return date_modified;
    }

    public void setDate_modified(Integer date_modified) {
        this.date_modified = date_modified;
    }
    

    
    
    
}
