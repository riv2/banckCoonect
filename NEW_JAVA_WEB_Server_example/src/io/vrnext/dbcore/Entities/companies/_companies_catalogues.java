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
@Table(name = "_companies_catalogues")
public class _companies_catalogues implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "company_uid")
    private Integer company_uid;
    
    @Column(name = "catalogue_uid")
    private Integer catalogue_uid;
    
    @Column(name = "hierarchy_uniq")
    private String hierarchy_uniq;
    
    @Column(name = "path")
    private String path;

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

    public Integer getCatalogue_uid() {
        return catalogue_uid;
    }

    public void setCatalogue_uid(Integer catalogue_uid) {
        this.catalogue_uid = catalogue_uid;
    }

    public String getHierarchy_uniq() {
        return hierarchy_uniq;
    }

    public void setHierarchy_uniq(String hierarchy_uniq) {
        this.hierarchy_uniq = hierarchy_uniq;
    }

    public String getPath() {
        return path;
    }

    public void setPath(String path) {
        this.path = path;
    }
    
   
    
    
    
}
