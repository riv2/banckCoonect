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
@Table(name = "_billing_gateways")
public class _billing_gateways implements Serializable {
       
    @Id
    @Column(name = "uniq")
    private String    uniq;    
     
    @Column(name = "name")
    private String name;
    
    @Column(name = "date_created")
    private String date_created;
    
    @Column(name = "date_updated")
    private String date_updated;
    
    @Column(name = "uniqs_currencies")
    private String uniqs_currencies;
    
    @Column(name = "advanced")
    private String advanced;
    
    @Column(name = "settings")
    private String settings;
    
    @Column(name = "enabled")
    private String enabled;

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

    public String getUniqs_currencies() {
        return uniqs_currencies;
    }

    public void setUniqs_currencies(String uniqs_currencies) {
        this.uniqs_currencies = uniqs_currencies;
    }

    public String getAdvanced() {
        return advanced;
    }

    public void setAdvanced(String advanced) {
        this.advanced = advanced;
    }

    public String getSettings() {
        return settings;
    }

    public void setSettings(String settings) {
        this.settings = settings;
    }

    public String getEnabled() {
        return enabled;
    }

    public void setEnabled(String enabled) {
        this.enabled = enabled;
    }
    
   
    
    
    
}
