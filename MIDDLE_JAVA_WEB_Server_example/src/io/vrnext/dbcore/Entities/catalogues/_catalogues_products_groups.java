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
@Table(name = "_catalogues_products_groups")
public class _catalogues_products_groups implements Serializable {
    @Id
    @Column(name = "uid")
    private Integer    uid;
    
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "group_uniq")
    private String group_uniq;
    
    @Column(name = "product_uniq")
    private String product_uniq;
    
    @Column(name = "date_modified")
    private Integer    date_modified;
    
    @Column(name = "date_deleted")
    private Integer    date_deleted;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public String getGroup_uniq() {
        return group_uniq;
    }

    public void setGroup_uniq(String group_uniq) {
        this.group_uniq = group_uniq;
    }

    public String getProduct_uniq() {
        return product_uniq;
    }

    public void setProduct_uniq(String product_uniq) {
        this.product_uniq = product_uniq;
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
    
     
  
}
