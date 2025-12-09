package com.dhanuka.indhan.Model;

/**
 * Created by John on 5/30/2018.
 */
public class PAtpApiModel {
    int product_id = 0;
    int data_source_id = 0;
    int crop_id = 0;
    int other_crop_1 = 0;
    int other_crop_2 = 0;
    int district_id = 0;
    int state_id = 0;
    int tehsil_id = 0;
    int language_id = 0;
    int territory_id = 0;
    int forwhom = 0;
    int expected_farmer = 0;
    int expected_pda = 0;
    String record_id = "";
    String plan_date = "";
    String village = "";
    int product_id_2 = 0;
    int product_id_3 = 0;
    String dd_id = "";

    String presenter_id = "";
    String presenter_type = "";
    String monitor_id = "";
    String monitor_type = "";

    public int getTerritory_id() {
        return territory_id;
    }

    public void setTerritory_id(int territory_id) {
        this.territory_id = territory_id;
    }

    public String getPresenter_id() {
        return presenter_id;
    }

    public void setPresenter_id(String presenter_id) {
        this.presenter_id = presenter_id;
    }

    public String getPresenter_type() {
        return presenter_type;
    }

    public void setPresenter_type(String presenter_type) {
        this.presenter_type = presenter_type;
    }

    public String getMonitor_id() {
        return monitor_id;
    }

    public void setMonitor_id(String monitor_id) {
        this.monitor_id = monitor_id;
    }

    public String getMonitor_type() {
        return monitor_type;
    }

    public void setMonitor_type(String monitor_type) {
        this.monitor_type = monitor_type;
    }

    public PAtpApiModel() {
    }

    public int getForwhom() {
        return forwhom;
    }

    public void setForwhom(int forwhom) {
        this.forwhom = forwhom;
    }

    public int getLanguage_id() {
        return language_id;
    }

    public void setLanguage_id(int language_id) {
        this.language_id = language_id;
    }

    public String getDd_id() {
        return dd_id;
    }

    public void setDd_id(String dd_id) {
        this.dd_id = dd_id;
    }

    public int getProduct_id_2() {
        return product_id_2;
    }

    public void setProduct_id_2(int product_id_2) {
        this.product_id_2 = product_id_2;
    }

    public int getProduct_id_3() {
        return product_id_3;
    }

    public void setProduct_id_3(int product_id_3) {
        this.product_id_3 = product_id_3;
    }

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public int getProduct_id() {
        return product_id;
    }

    public void setProduct_id(int product_id) {
        this.product_id = product_id;
    }

    public int getData_source_id() {
        return data_source_id;
    }

    public void setData_source_id(int data_source_id) {
        this.data_source_id = data_source_id;
    }

    public int getCrop_id() {
        return crop_id;
    }

    public void setCrop_id(int crop_id) {
        this.crop_id = crop_id;
    }

    public int getCrop_id1() {
        return other_crop_1;
    }

    public void setCrop_id1(int other_crop_1) {
        this.other_crop_1 = other_crop_1;
    }

    public int getCrop_id2() {
        return other_crop_2;
    }

    public void setCrop_id2(int other_crop_2) {
        this.other_crop_2 = other_crop_2;
    }

    public int getDistrict_id() {
        return district_id;
    }

    public void setDistrict_id(int district_id) {
        this.district_id = district_id;
    }

    public int getState_id() {
        return state_id;
    }

    public void setState_id(int state_id) {
        this.state_id = state_id;
    }

    public int getTehsil_id() {
        return tehsil_id;
    }

    public void setTehsil_id(int tehsil_id) {
        this.tehsil_id = tehsil_id;
    }

    public int getExpected_farmer() {
        return expected_farmer;
    }

    public void setExpected_farmer(int expected_farmer) {
        this.expected_farmer = expected_farmer;
    }

    public int getExpected_pda() {
        return expected_pda;
    }

    public void setExpected_pda(int expected_pda) {
        this.expected_pda = expected_pda;
    }

    public String getRecord_id() {
        return record_id;
    }

    public void setRecord_id(String record_id) {
        this.record_id = record_id;
    }

    public String getPlan_date() {
        return plan_date;
    }

    public void setPlan_date(String plan_date) {
        this.plan_date = plan_date;
    }
}