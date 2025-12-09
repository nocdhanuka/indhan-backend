package com.dhanuka.indhan.Model;

public class CompanyModel {
    long id = System.currentTimeMillis();
    CompanyListResponse.Data categoryData;
    double volumeData;
    Boolean isEdit = false;

    public Boolean getEdit() {
        return isEdit;
    }

    public void setEdit(Boolean edit) {
        isEdit = edit;
    }

    public long getId() {
        return id;
    }

    public CompanyListResponse.Data getCategoryData() {
        return categoryData;
    }

    public void setCategoryData(CompanyListResponse.Data categoryData) {
        this.categoryData = categoryData;
    }

    public double getVolumeData() {
        return volumeData;
    }

    public void setVolumeData(double volumeData) {
        this.volumeData = volumeData;
    }
}
