package com.dhanuka.indhan.Activity.NewTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName
import java.util.*

/**
 * Created by Shivam on 2/28/2018.
 */

class NewTicketBean {

    @SerializedName("status_code")
    @Expose
    var status_code: Int? = null

    @SerializedName("message")
    @Expose
    var message: String? = null

    @SerializedName("status")
    @Expose
    var status: String? = null

    @SerializedName("data")
    @Expose
    var data: Data? = null

    inner class Data {

        @SerializedName("data_source_list")
        @Expose
        var data_source: ArrayList<DataSourceBean>? = null

        @SerializedName("state_list")
        @Expose
        var state_list: ArrayList<StateBean>? = null

        @SerializedName("product_list")
        @Expose
        var product_list: ArrayList<ProductBean>? = null

        @SerializedName("crop_list")
        @Expose
        var crop_list: ArrayList<CropBean>? = null
    }
}
