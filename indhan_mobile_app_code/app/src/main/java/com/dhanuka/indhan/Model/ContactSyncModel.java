package com.dhanuka.indhan.Model;

import java.util.ArrayList;

/**
 * Created by John on 4/6/2018.
 */
public class ContactSyncModel {
    private ArrayList<RegisterFarmerContactModel> contactModelList;
    private String apiToken;


    public ArrayList<RegisterFarmerContactModel> getContactModelList() {
        return contactModelList;
    }

    public void setContactModelList(ArrayList<RegisterFarmerContactModel> contactModelList) {
        this.contactModelList = contactModelList;
    }

    public String getApiToken() {
        return apiToken;
    }

    public void setApiToken(String apiToken) {
        this.apiToken = apiToken;
    }
}
