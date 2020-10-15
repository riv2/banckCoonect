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
@Table(name = "_billing_discounts")
public class _billing_discounts implements Serializable {
       
    @Id
    @Column(name = "uniq")
    private Integer    uniq;    
     
    @Column(name = "uniq_mamber")
    private String uniq_mamber;
    
    @Column(name = "uniq_company")
    private String uniq_company;
    
    @Column(name = "sum")
    private float sum;
    
    @Column(name = "type")
    private String type;
    
    @Column(name = "advanced")
    private String advanced;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date_updated")
    private String date_updated;

    public Integer getUniq() {
        return uniq;
    }

    public void setUniq(Integer uniq) {
        this.uniq = uniq;
    }

    public String getUniq_mamber() {
        return uniq_mamber;
    }

    public void setUniq_mamber(String uniq_mamber) {
        this.uniq_mamber = uniq_mamber;
    }

    public String getUniq_company() {
        return uniq_company;
    }

    public void setUniq_company(String uniq_company) {
        this.uniq_company = uniq_company;
    }

    public float getSum() {
        return sum;
    }

    public void setSum(float sum) {
        this.sum = sum;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getAdvanced() {
        return advanced;
    }

    public void setAdvanced(String advanced) {
        this.advanced = advanced;
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
