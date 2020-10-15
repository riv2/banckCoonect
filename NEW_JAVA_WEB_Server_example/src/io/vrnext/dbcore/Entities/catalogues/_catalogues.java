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
@Table(name = "_catalogues")
public class _catalogues implements Serializable {

    @Column(name = "name")
    private String name;
     
    @Column(name = "type")
    private String type;
     
    @Column(name = "access")
    private Integer    access;
     
    @Column(name = "date_created")
    private Integer    date_created;    
    
    @Id
    @Column(name = "uid")
    private Integer    uid;
    
    @Column(name = "owner")
    private String owner;
    
    @Column(name = "company_uid")
    private Integer company_uid;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public Integer getAccess() {
        return access;
    }

    public void setAccess(Integer access) {
        this.access = access;
    }

    public Integer getDate_created() {
        return date_created;
    }

    public void setDate_created(Integer date_created) {
        this.date_created = date_created;
    }

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getOwner() {
        return owner;
    }

    public void setOwner(String owner) {
        this.owner = owner;
    }

    public Integer getCompany_uid() {
        return company_uid;
    }

    public void setCompany_uid(Integer company_uid) {
        this.company_uid = company_uid;
    }

   
   
   
    
    
    
}
