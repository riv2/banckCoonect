package io.vrnext.dbcore.entities.catalogues;

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
@Table(name = "_catalogues_products")
public class _catalogues_products implements Serializable {
    
    @Id
    @Column(name = "uniq")
    private String uniq;
    
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "manufactorer")
    private String manufactorer;
    
    @Column(name = "dim_x")
    private Integer    dim_x;
    
    @Column(name = "dim_y")
    private Integer    dim_y;
    
    @Column(name = "dim_z")
    private Integer    dim_z;
    
    @Column(name = "flags")
    private Integer    flags;
    
    @Column(name = "status")
    private Integer    status;
    
    @Column(name = "date_modified")
    private Integer    date_modified;
    
    @Column(name = "date_deleted")
    private Integer    date_deleted;
    
    @Column(name = "catalogue_uid")
    private Integer    catalogue_uid;
    
    @Column(name = "article")
    private String    article;
    
    @Column(name = "available")
    private String    available;
    
    @Column(name = "price")
    private String    price;
    
    @Column(name = "units")
    private String    units;
    
     @Column(name = "company_uid")
    private String    company_uid;

    @Column(name = "name")
    private String name;  

    
    @Column(name = "checksum")
    private String checksum;  

    public String getUniq() {
        return uniq;
    }

    public void setUniq(String uniq) {
        this.uniq = uniq;
    }

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public String getManufactorer() {
        return manufactorer;
    }

    public void setManufactorer(String manufactorer) {
        this.manufactorer = manufactorer;
    }

    public Integer getDim_x() {
        return dim_x;
    }

    public void setDim_x(Integer dim_x) {
        this.dim_x = dim_x;
    }

    public Integer getDim_y() {
        return dim_y;
    }

    public void setDim_y(Integer dim_y) {
        this.dim_y = dim_y;
    }

    public Integer getDim_z() {
        return dim_z;
    }

    public void setDim_z(Integer dim_z) {
        this.dim_z = dim_z;
    }

    public Integer getFlags() {
        return flags;
    }

    public void setFlags(Integer flags) {
        this.flags = flags;
    }

    public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }

    public Integer getDate_modified() {
        return date_modified;
    }

    public void setDate_modified(Integer date_modified) {
        this.date_modified = date_modified;
    }

    public Integer getDate_deleted() {
        return date_deleted;
    }

    public void setDate_deleted(Integer date_deleted) {
        this.date_deleted = date_deleted;
    }

    public Integer getCatalogue_uid() {
        return catalogue_uid;
    }

    public void setCatalogue_uid(Integer catalogue_uid) {
        this.catalogue_uid = catalogue_uid;
    }

    public String getArticle() {
        return article;
    }

    public void setArticle(String article) {
        this.article = article;
    }

    public String getAvailable() {
        return available;
    }

    public void setAvailable(String available) {
        this.available = available;
    }

    public String getPrice() {
        return price;
    }

    public void setPrice(String price) {
        this.price = price;
    }

    public String getUnits() {
        return units;
    }

    public void setUnits(String units) {
        this.units = units;
    }

    public String getCompany_uid() {
        return company_uid;
    }

    public void setCompany_uid(String company_uid) {
        this.company_uid = company_uid;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getChecksum() {
        return checksum;
    }

    public void setChecksum(String checksum) {
        this.checksum = checksum;
    }

    
 
}
