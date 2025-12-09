package com.dhanuka.indhan.DataBase.TableDataModel;

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.Date;

/**
 * Created by John on 5/29/2018.
 */
public class PlanAtpDetail implements Comparable<PlanAtpDetail> {

    @SerializedName("plan_atp_id")
    @Expose
    private Integer planAtpId;
    @SerializedName("id")
    @Expose
    private Integer id;
    @SerializedName("name")
    @Expose
    private String name;
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
    @SerializedName("plan_date")
    @Expose
    private String planDate;
    @SerializedName("other_crop_1")
    @Expose
    private String product1;
    @SerializedName("other_crop_2")
    @Expose
    private String product2;
    private Date date_obj;

    public String getProduct1() {
        return product1;
    }

    public void setProduct1(String product1) {
        this.product1 = product1;
    }

    public String getProduct2() {
        return product2;
    }

    public void setProduct2(String product2) {
        this.product2 = product2;
    }

    public Date getDate_obj() {
        return date_obj;
    }

    public void setDate_obj(Date date_obj) {
        this.date_obj = date_obj;
    }

    public Integer getPlanAtpId() {
        return planAtpId;
    }

    public void setPlanAtpId(Integer planAtpId) {
        this.planAtpId = planAtpId;
    }

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getProduct() {
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

    public String getPlanDate() {
        return planDate;
    }

    public void setPlanDate(String planDate) {
        this.planDate = planDate;
    }

    @Override
    public int compareTo(@NonNull PlanAtpDetail planAtpDetail) {
        return planAtpDetail.getDate_obj().compareTo(getDate_obj());
    }
}