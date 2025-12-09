package com.dhanuka.indhan.Activity.EditTicketMVP.Model

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean
import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class EditTicketBean {
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
        @SerializedName("data_source")
        @Expose
        var data_source: ArrayList<DataSourceBean>? = null

        @SerializedName("state_list")
        @Expose
        var state_list: ArrayList<StateBean>? = null

        @SerializedName("tehsil_list")
        @Expose
        var tehsil_list: ArrayList<TehsilBean>? = null

        @SerializedName("district_list")
        @Expose
        var district_list: ArrayList<DistrictBean>? = null

        @SerializedName("product_list")
        @Expose
        var product_list: ArrayList<ProductBean>? = null

        @SerializedName("crop_list")
        @Expose
        var crop_list: ArrayList<CropBean>? = null

        @SerializedName("ticket_data")
        @Expose
        var ticket_data: ArrayList<OldTicketDataBean>? = null
    }
}
