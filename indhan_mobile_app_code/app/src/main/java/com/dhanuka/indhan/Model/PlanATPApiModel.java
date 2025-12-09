package com.dhanuka.indhan.Model;

import java.util.ArrayList;

/**
 * Created by John on 4/6/2018.
 */
public class PlanATPApiModel {
    private ArrayList<PAtpApiModel> planATP;
    private String apiToken;

    public ArrayList<PAtpApiModel> getPlanATP() {
        return planATP;
    }

    public void setPlanATP(ArrayList<PAtpApiModel> planATP) {
        this.planATP = planATP;
    }

    public String getApiToken() {

        return apiToken;
    }

    public void setApiToken(String apiToken) {
        this.apiToken = apiToken;
    }


}
