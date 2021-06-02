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
@Table(name = "_companies_branches")
public class _companies_branches implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "company_uid")
    private Integer company_uid;
    
    @Column(name = "name")
    private String name;
    
    @Column(name = "address")
    private String address;

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

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }
    

    
    
}
