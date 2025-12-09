package com.dhanuka.indhan.Model;

public class CropModel {
    Long id = System.currentTimeMillis();
    String subcategoryData;
    FoodCategoryResponse.Data categoryData;
    double volumeData;
    boolean isEdit = false;

    public Long getId() {
        return id;
    }

    public boolean isEdit() {
        return isEdit;
    }

    public void setEdit(boolean edit) {
        isEdit = edit;
    }

    public String getSubcategoryData() {
        return subcategoryData;
    }

    public void setSubcategoryData(String subcategoryData) {
        this.subcategoryData = subcategoryData;
    }

    public FoodCategoryResponse.Data getCategoryData() {
        return categoryData;
    }

    public void setCategoryData(FoodCategoryResponse.Data categoryData) {
        this.categoryData = categoryData;
    }

    public double getVolumeData() {
        return volumeData;
    }

    public void setVolumeData(double volumeData) {
        this.volumeData = volumeData;
    }
}
