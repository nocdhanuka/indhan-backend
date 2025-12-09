package com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class DistrictModel {
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
    var data: ArrayList<DistrictBean>? = null
}
