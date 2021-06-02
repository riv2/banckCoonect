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
@Table(name = "_companies")
public class _companies implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "name")
    private String name;
    
    @Column(name = "hq_address")
    private String hq_address;
    
    @Column(name = "logo")
    private String logo;
    
    @Column(name = "corporate_id")
    private String corporate_id;
    
    @Column(name = "followers")
    private String followers;
    
    @Column(name = "country")
    private String country;
    
    @Column(name = "city")
    private String city;
    
    @Column(name = "owner")
    private String owner;
    
    @Column(name = "hidden")
    private Integer hidden;
    
    @Column(name = "allow")
    private Integer allow;
    
    @Column(name = "phone")
    private String phone;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getHq_address() {
        return hq_address;
    }

    public void setHq_address(String hq_address) {
        this.hq_address = hq_address;
    }

    public String getLogo() {
        return logo;
    }

    public void setLogo(String logo) {
        this.logo = logo;
    }

    public String getCorporate_id() {
        return corporate_id;
    }

    public void setCorporate_id(String corporate_id) {
        this.corporate_id = corporate_id;
    }

    public String getFollowers() {
        return followers;
    }

    public void setFollowers(String followers) {
        this.followers = followers;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getOwner() {
        return owner;
    }

    public void setOwner(String owner) {
        this.owner = owner;
    }

    public Integer getHidden() {
        return hidden;
    }

    public void setHidden(Integer hidden) {
        this.hidden = hidden;
    }

    public Integer getAllow() {
        return allow;
    }

    public void setAllow(Integer allow) {
        this.allow = allow;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }
    
    
    
}
