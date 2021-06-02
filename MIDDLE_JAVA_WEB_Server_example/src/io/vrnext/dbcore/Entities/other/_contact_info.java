package io.vrnext.dbcore.entities.other;

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
@Table(name = "_contact_info")
public class _contact_info implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "ref_type")
    private String ref_type;
    
    @Column(name = "ref_uid")
    private Integer ref_uid;
    
    @Column(name = "type")
    private String type;
    
    @Column(name = "value")
    private String value;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getRef_type() {
        return ref_type;
    }

    public void setRef_type(String ref_type) {
        this.ref_type = ref_type;
    }

    public Integer getRef_uid() {
        return ref_uid;
    }

    public void setRef_uid(Integer ref_uid) {
        this.ref_uid = ref_uid;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    
}
