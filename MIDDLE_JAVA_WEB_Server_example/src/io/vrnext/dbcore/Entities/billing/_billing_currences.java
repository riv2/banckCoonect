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
@Table(name = "_billing_currences")
public class _billing_currences implements Serializable {
       
    @Id
    @Column(name = "uniq")
    private String    uniq;    
     
    @Column(name = "name")
    private String name;
    
    @Column(name = "ratio")
    private float ratio;
    
    @Column(name = "code")
    private String code;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date_updated")
    private String date_updated;

    public String getUniq() {
        return uniq;
    }

    public void setUniq(String uniq) {
        this.uniq = uniq;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public float getRatio() {
        return ratio;
    }

    public void setRatio(float ratio) {
        this.ratio = ratio;
    }

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
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

    
    
}
