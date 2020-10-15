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
@Table(name = "_members_devices")
public class _members_devices implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "device_uniq")
    private String device_uniq;
    
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "ip_address")
    private String ip_address;
    
    @Column(name = "device_name")
    private String device_name;
    
    @Column(name = "date_registered")
    private Integer date_registered;
    
    @Column(name = "date_updated")
    private Integer date_updated;
    
    @Column(name = "corporate")
    private Integer corporate;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getDevice_uniq() {
        return device_uniq;
    }

    public void setDevice_uniq(String device_uniq) {
        this.device_uniq = device_uniq;
    }

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public String getIp_address() {
        return ip_address;
    }

    public void setIp_address(String ip_address) {
        this.ip_address = ip_address;
    }

    public String getDevice_name() {
        return device_name;
    }

    public void setDevice_name(String device_name) {
        this.device_name = device_name;
    }

    public Integer getDate_registered() {
        return date_registered;
    }

    public void setDate_registered(Integer date_registered) {
        this.date_registered = date_registered;
    }

    public Integer getDate_updated() {
        return date_updated;
    }

    public void setDate_updated(Integer date_updated) {
        this.date_updated = date_updated;
    }

    public Integer getCorporate() {
        return corporate;
    }

    public void setCorporate(Integer corporate) {
        this.corporate = corporate;
    }
    
 
    
    
}
