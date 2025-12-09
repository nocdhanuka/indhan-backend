package com.dhanuka.indhan.PlanSyllabusForBdo.Model;

/**
 * Created by John on 5/7/2018.
 */
public class SyllabusApiProduct {


    int month;
    int crop_id;
    int product_id;
    int dd;
    int days;
    int year;
    int bdo_id;
    String record_id;

    public String getRecord_id() {
        return record_id;
    }

    public void setRecord_id(String record_id) {
        this.record_id = record_id;
    }

    public int getMonth() {
        return month;
    }

    public void setMonth(int month) {
        this.month = month;
    }

    public int getCrop_id() {
        return crop_id;
    }

    public void setCrop_id(int crop_id) {
        this.crop_id = crop_id;
    }

    public int getProduct_id() {
        return product_id;
    }

    public void setProduct_id(int product_id) {
        this.product_id = product_id;
    }

    public int getDd() {
        return dd;
    }

    public void setDd(int dd) {
        this.dd = dd;
    }

    public int getDays() {
        return days;
    }

    public void setDays(int days) {
        this.days = days;
    }

    public int getBdo_id() {
        return bdo_id;
    }

    public void setBdo_id(int bdo_id) {
        this.bdo_id = bdo_id;
    }

    public int getYear() {
        return year;
    }

    public void setYear(int year) {
        this.year = year;
    }
}
