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
@Table(name = "_companies_members")
public class _companies_members implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer    uid;    
     
    @Column(name = "company_uid")
    private Integer company_uid;
    
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "branch_uid")
    private Integer branch_uid;
    
    @Column(name = "is_default")
    private Integer is_default;
    
    @Column(name = "is_owner")
    private Integer is_owner;
    
    @Column(name = "is_admin")
    private Integer is_admin;

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

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public Integer getBranch_uid() {
        return branch_uid;
    }

    public void setBranch_uid(Integer branch_uid) {
        this.branch_uid = branch_uid;
    }

    public Integer getIs_default() {
        return is_default;
    }

    public void setIs_default(Integer is_default) {
        this.is_default = is_default;
    }

    public Integer getIs_owner() {
        return is_owner;
    }

    public void setIs_owner(Integer is_owner) {
        this.is_owner = is_owner;
    }

    public Integer getIs_admin() {
        return is_admin;
    }

    public void setIs_admin(Integer is_admin) {
        this.is_admin = is_admin;
    }
    

    
    
    
}
