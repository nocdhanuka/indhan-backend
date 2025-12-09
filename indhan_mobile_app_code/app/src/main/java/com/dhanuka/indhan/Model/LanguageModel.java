package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

/**
 * Created by Shivam on 3/9/2018.
 */

public class LanguageModel {

    @SerializedName("id")
    @Expose
    private String id;

    @SerializedName("language")
    @Expose
    private String language;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getLanguage() {
        return language;
    }

    public void setLanguage(String language) {
        this.language = language;
    }
}
