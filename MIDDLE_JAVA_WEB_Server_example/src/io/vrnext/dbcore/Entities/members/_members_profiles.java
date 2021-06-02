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
@Table(name = "_members_profiles")
public class _members_profiles implements Serializable {
       
    @Id
    @Column(name = "member_uniq")
    private String member_uniq;    
     
    @Column(name = "first_name")
    private String first_name;
    
    @Column(name = "last_name")
    private String last_name;
    
    @Column(name = "registered")
    private Integer registered;
    
    @Column(name = "last_logged_in")
    private Integer last_logged_in;

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public String getFirst_name() {
        return first_name;
    }

    public void setFirst_name(String first_name) {
        this.first_name = first_name;
    }

    public String getLast_name() {
        return last_name;
    }

    public void setLast_name(String last_name) {
        this.last_name = last_name;
    }

    public Integer getRegistered() {
        return registered;
    }

    public void setRegistered(Integer registered) {
        this.registered = registered;
    }

    public Integer getLast_logged_in() {
        return last_logged_in;
    }

    public void setLast_logged_in(Integer last_logged_in) {
        this.last_logged_in = last_logged_in;
    }
    
   
    
    
    
}
