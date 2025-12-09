package com.dhanuka.indhan.PlanATP.Model


import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.google.gson.annotations.SerializedName

data class DataSourceListBean(
    @SerializedName("data")
    val `data`: ArrayList<DataSourceBean>,
    @SerializedName("message")
    val message: String, // Data source list!
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
)