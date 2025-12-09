package com.dhanuka.indhan.SyllabusHistory.Model;

/**
 * Created by John on 5/14/2018.
 */
public class BDListForSyllabusModel {
    int bdo_id, noOdDays;
    String month, bdo_name;
    int submitted_noOfDays;


    public int getSubmitted_noOfDays() {
        return submitted_noOfDays;
    }

    public void setSubmitted_noOfDays(int submitted_noOfDays) {
        this.submitted_noOfDays = submitted_noOfDays;
    }

    public int getBdo_id() {
        return bdo_id;
    }

    public void setBdo_id(int bdo_id) {
        this.bdo_id = bdo_id;
    }

    public int getNoOdDays() {
        return noOdDays;
    }

    public void setNoOdDays(int noOdDays) {
        this.noOdDays = noOdDays;
    }

    public String getMonth() {
        return month;
    }

    public void setMonth(String month) {
        this.month = month;
    }

    public String getBdo_name() {
        return bdo_name;
    }

    public void setBdo_name(String bdo_name) {
        this.bdo_name = bdo_name;
    }
}
