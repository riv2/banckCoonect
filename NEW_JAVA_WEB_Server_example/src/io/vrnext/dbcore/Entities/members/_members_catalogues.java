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
@Table(name = "_members_catalogues")
public class _members_catalogues implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "hierarchy_uniq")
    private String hierarchy_uniq;
    
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "catalogue_uid")
    private Integer catalogue_uid;
    
    @Column(name = "path")
    private String path;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getHierarchy_uniq() {
        return hierarchy_uniq;
    }

    public void setHierarchy_uniq(String hierarchy_uniq) {
        this.hierarchy_uniq = hierarchy_uniq;
    }

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public Integer getCatalogue_uid() {
        return catalogue_uid;
    }

    public void setCatalogue_uid(Integer catalogue_uid) {
        this.catalogue_uid = catalogue_uid;
    }

    public String getPath() {
        return path;
    }

    public void setPath(String path) {
        this.path = path;
    }
    
   
    
    
}
