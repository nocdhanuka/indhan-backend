package com.dhanuka.indhan.ActivateTicket.Model;

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;

/**
 * Created by John on 7/10/2018.
 */
public class AtpList implements Serializable, Comparable<AtpList> {

    Date date;
    @SerializedName("product")
    @Expose
    private String product;
    @SerializedName("crop")
    @Expose
    private String crop;
    @SerializedName("data_source")
    @Expose
    private String dataSource;
    @SerializedName("state")
    @Expose
    private String state;
    @SerializedName("district")
    @Expose
    private String district;
    @SerializedName("tehsil")
    @Expose
    private String tehsil;
    @SerializedName("village")
    @Expose
    private String village;
    @SerializedName("expected_farmer")
    @Expose
    private Integer expectedFarmer;
    @SerializedName("expected_pda")
    @Expose
    private Integer expectedPda;
    @SerializedName("record_id")
    @Expose
    private String recordId;
    @SerializedName("plan_date")
    @Expose
    private String planDate;
    @SerializedName("plan_atp_id")
    @Expose
    private Integer planAtpId;
    @SerializedName("status")
    @Expose
    private Integer status;
    @SerializedName("other_crop_1")
    @Expose
    private String other_crop_1;
    @SerializedName("other_crop_2")
    @Expose
    private String other_crop_2;
    @SerializedName("language")
    @Expose
    private String language;
    @SerializedName("language_id")
    @Expose
    private String language_id;
    private Integer isValid;

    public String getLanguage() {
        return language;
    }

    public void setLanguage(String language) {
        this.language = language;
    }

    public String getLanguage_id() {
        return language_id;
    }

    public void setLanguage_id(String language_id) {
        this.language_id = language_id;
    }

    public String getOther_crop_1() {
        return other_crop_1;
    }

    public void setOther_crop_1(String other_crop_1) {
        this.other_crop_1 = other_crop_1;
    }

    public String getOther_crop_2() {
        return other_crop_2;
    }

    public void setOther_crop_2(String other_crop_2) {
        this.other_crop_2 = other_crop_2;
    }

    public Integer getIsValid() {
        return isValid;
    }

    public void setIsValid(Integer isValid) {
        this.isValid = isValid;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public String getProduct() {
        if(product ==null){
            product = "";
        }
        return product;
    }

    public void setProduct(String product) {
        this.product = product;
    }

    public String getCrop() {
        return crop;
    }

    public void setCrop(String crop) {
        this.crop = crop;
    }

    public String getDataSource() {
        return dataSource;
    }

    public void setDataSource(String dataSource) {
        this.dataSource = dataSource;
    }

    public String getState() {
        return state;
    }

    public void setState(String state) {
        this.state = state;
    }

    public String getDistrict() {
        return district;
    }

    public void setDistrict(String district) {
        this.district = district;
    }

    public String getTehsil() {
        return tehsil;
    }

    public void setTehsil(String tehsil) {
        this.tehsil = tehsil;
    }

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public Integer getExpectedFarmer() {
        return expectedFarmer;
    }

    public void setExpectedFarmer(Integer expectedFarmer) {
        this.expectedFarmer = expectedFarmer;
    }

    public Integer getExpectedPda() {
        return expectedPda;
    }

    public void setExpectedPda(Integer expectedPda) {
        this.expectedPda = expectedPda;
    }

    public String getRecordId() {
        return recordId;
    }

    public void setRecordId(String recordId) {
        this.recordId = recordId;
    }

    public String getPlanDate() {
        return planDate;
    }

    public void setPlanDate(String planDate) {
        this.planDate = planDate;
    }

    public Integer getPlanAtpId() {
        return planAtpId;
    }

    public void setPlanAtpId(Integer planAtpId) {
        this.planAtpId = planAtpId;
    }

    public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }

    @Override
    public int compareTo(@NonNull AtpList atpList) {
        return getDate().compareTo(atpList.getDate());
    }
}