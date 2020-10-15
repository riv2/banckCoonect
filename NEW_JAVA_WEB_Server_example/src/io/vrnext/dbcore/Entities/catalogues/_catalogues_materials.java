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
@Table(name = "_catalogues_materials")
public class _catalogues_materials implements Serializable {

    @Id
    @Column(name = "uniq")
    private String uniq;

    @Column(name = "diffuse")
    private Integer diffuse;

    @Column(name = "specular")
    private Integer specular;

    @Column(name = "reflection")
    private Integer reflection;

    @Column(name = "ior")
    private Integer ior;

    @Column(name = "material_type")
    private Integer material_type;

    @Column(name = "date_modified")
    private Integer date_modified;

    @Column(name = "date_deleted")
    private Integer date_deleted;
    
    @Column(name = "catalogue_uid")
    private Integer catalogue_uid;

    public String getUniq() {
        return uniq;
    }

    public void setUniq(String uniq) {
        this.uniq = uniq;
    }

    public Integer getDiffuse() {
        return diffuse;
    }

    public void setDiffuse(Integer diffuse) {
        this.diffuse = diffuse;
    }

    public Integer getSpecular() {
        return specular;
    }

    public void setSpecular(Integer specular) {
        this.specular = specular;
    }

    public Integer getReflection() {
        return reflection;
    }

    public void setReflection(Integer reflection) {
        this.reflection = reflection;
    }

    public Integer getIor() {
        return ior;
    }

    public void setIor(Integer ior) {
        this.ior = ior;
    }

    public Integer getMaterial_type() {
        return material_type;
    }

    public void setMaterial_type(Integer material_type) {
        this.material_type = material_type;
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

    public Integer getCatalogue_uid() {
        return catalogue_uid;
    }

    public void setCatalogue_uid(Integer catalogue_uid) {
        this.catalogue_uid = catalogue_uid;
    }
    

   
}
