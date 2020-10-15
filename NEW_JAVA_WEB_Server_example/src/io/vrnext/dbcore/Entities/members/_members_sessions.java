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
@Table(name = "_members_sessions")
public class _members_sessions implements Serializable {
       
    @Id
    @Column(name = "uid")
    private Integer uid;    
     
    @Column(name = "member_uniq")
    private String member_uniq;
    
    @Column(name = "session_uniq")
    private String session_uniq;
    
    @Column(name = "date_expires")
    private Integer date_expires;

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }

    public String getMember_uniq() {
        return member_uniq;
    }

    public void setMember_uniq(String member_uniq) {
        this.member_uniq = member_uniq;
    }

    public String getSession_uniq() {
        return session_uniq;
    }

    public void setSession_uniq(String session_uniq) {
        this.session_uniq = session_uniq;
    }

    public Integer getDate_expires() {
        return date_expires;
    }

    public void setDate_expires(Integer date_expires) {
        this.date_expires = date_expires;
    }
    
  
    
    
}
