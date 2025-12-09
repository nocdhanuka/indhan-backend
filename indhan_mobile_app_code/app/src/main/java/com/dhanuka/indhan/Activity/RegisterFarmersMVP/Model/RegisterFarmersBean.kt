package com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model

import com.dhanuka.indhan.Model.AddNumberModel
import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName
import java.util.*

/**
 * Created by Shivam on 3/8/2018.
 */

class RegisterFarmersBean {

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
    var data: ArrayList<FarmersTicketBean>? = null

    @SerializedName("manual_data")
    @Expose
    var manual_data: ArrayList<AddNumberModel>? = null

}
