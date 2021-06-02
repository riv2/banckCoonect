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
@Table(name="_catalogues_materials_references")
public class _catalogues_materials_references implements Serializable  {
    
    @Id
    @Column(name="material_uniq")
    public String material_uniq;
    
    @Column(name="material_channel")
    public Integer    material_channel;
    
    @Column(name="reference_uniq")
    public String reference_uniq;
    
    @Column(name="reference_type")
    public Integer    reference_type;
    
    @Column(name="date_modified")
    public Integer    date_modified;
    
    @Column(name="date_deleted")
    public Integer    date_deleted;
    
    @Column(name="dim_x")
    public float  dim_x;
    
    @Column(name="dim_y")
    public float  dim_y;
    
    @Column(name="offset_x")
    public float  offset_x;
    
    @Column(name="offset_y")
    public float  offset_y;
    
    @Column(name="uid")
    public Integer    uid;

    public _catalogues_materials_references() { } 

    public String getMaterial_uniq() {
        return material_uniq;
    }

    public void setMaterial_uniq(String material_uniq) {
        this.material_uniq = material_uniq;
    }

    public Integer getMaterial_channel() {
        return material_channel;
    }

    public void setMaterial_channel(Integer material_channel) {
        this.material_channel = material_channel;
    }

    public String getReference_uniq() {
        return reference_uniq;
    }

    public void setReference_uniq(String reference_uniq) {
        this.reference_uniq = reference_uniq;
    }

    public Integer getReference_type() {
        return reference_type;
    }

    public void setReference_type(Integer reference_type) {
        this.reference_type = reference_type;
    }

    public Integer getDate_modified() {
        return date_modified;
    }

    public void setDate_modified(Integer date_modified) {
        this.date_modified = date_modified;
    }

    public Integer getDate_deleted() {
        return date_deleted;
    }

    public void setDate_deleted(Integer date_deleted) {
        this.date_deleted = date_deleted;
    }

    public float getDim_x() {
        return dim_x;
    }

    public void setDim_x(float dim_x) {
        this.dim_x = dim_x;
    }

    public float getDim_y() {
        return dim_y;
    }

    public void setDim_y(float dim_y) {
        this.dim_y = dim_y;
    }

    public float getOffset_x() {
        return offset_x;
    }

    public void setOffset_x(float offset_x) {
        this.offset_x = offset_x;
    }

    public float getOffset_y() {
        return offset_y;
    }

    public void setOffset_y(float offset_y) {
        this.offset_y = offset_y;
    }

    public Integer getUid() {
        return uid;
    }

    public void setUid(Integer uid) {
        this.uid = uid;
    }
    
}
