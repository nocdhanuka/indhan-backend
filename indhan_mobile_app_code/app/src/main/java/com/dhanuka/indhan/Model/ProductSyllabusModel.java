package com.dhanuka.indhan.Model;

import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusApiProduct;

import java.util.ArrayList;

/**
 * Created by John on 4/6/2018.
 */
public class ProductSyllabusModel {
    private ArrayList<SyllabusApiProduct> planData;
    private String apiToken;

    public ArrayList<SyllabusApiProduct> getPlanData() {
        return planData;
    }

    public void setPlanData(ArrayList<SyllabusApiProduct> planData) {
        this.planData = planData;
    }

    public String getApiToken() {

        return apiToken;
    }

    public void setApiToken(String apiToken) {
        this.apiToken = apiToken;
    }
}
