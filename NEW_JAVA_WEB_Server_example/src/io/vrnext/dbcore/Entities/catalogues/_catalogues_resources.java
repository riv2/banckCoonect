/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package io.vrnext.dbcore.entities.catalogues;


import java.io.Serializable;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;

/**
 *
 * @author riv
 */

@Entity
@Table(name="_catalogues_resources")
public class _catalogues_resources implements Serializable {
    
    @Id
    @Column(name="uniq")
    public String uniq;
    
    @Column(name="path_source")
    public String path_source;
    
    @Column(name="checksum")
    public String checksum;
    
    @Column(name="date_modified")
    public Integer    date_modified;
        
    @Column(name="size_factor")
    public Integer  size_factor;
    
    public _catalogues_resources() {}   

    public String getUniq() {
        return uniq;
    }

    public void setUniq(String uniq) {
        this.uniq = uniq;
    }

    public String getPath_source() {
        return path_source;
    }

    public void setPath_source(String path_source) {
        this.path_source = path_source;
    }

    public String getChecksum() {
        return checksum;
    }

    public void setChecksum(String checksum) {
        this.checksum = checksum;
    }

    public Integer getDate_modified() {
        return date_modified;
    }

    public void setDate_modified(Integer date_modified) {
        this.date_modified = date_modified;
    }

    public Integer getSize_factor() {
        return size_factor;
    }

    public void setSize_factor(Integer size_factor) {
        this.size_factor = size_factor;
    }
    
}
