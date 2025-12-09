package com.dhanuka.indhan.Activity.TicketDetailMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketDataBean {

    @SerializedName("data_source")
    @Expose
    var data_source: String? = null

    @SerializedName("state")
    @Expose
    var state: String? = null

    @SerializedName("district")
    @Expose
    var district: String? = null

    @SerializedName("tehsil")
    @Expose
    var tehsil: String? = null

    @SerializedName("village")
    @Expose
    var village: String? = null

    @SerializedName("product_1")
    @Expose
    var product_1: String? = null

    @SerializedName("crop_1")
    @Expose
    var crop_1: String? = null

    @SerializedName("product_2")
    @Expose
    var product_2: String? = null

    @SerializedName("crop_2")
    @Expose
    var crop_2: String? = null

    @SerializedName("product_3")
    @Expose
    var product_3: String? = null

    @SerializedName("crop_3")
    @Expose
    var crop_3: String? = null

    @SerializedName("sp_name_1")
    @Expose
    var sp_name_1: String? = null

    @SerializedName("sp_number_1")
    @Expose
    var sp_number_1: String? = null

    @SerializedName("sp_name_2")
    @Expose
    var sp_name_2: String? = null

    @SerializedName("sp_number_2")
    @Expose
    var sp_number_2: String? = null

    @SerializedName("sp_name_3")
    @Expose
    var sp_name_3: String? = null

    @SerializedName("sp_number_3")
    @Expose
    var sp_number_3: String? = null

    @SerializedName("sp_name_4")
    @Expose
    var sp_name_4: String? = null

    @SerializedName("sp_number_4")
    @Expose
    var sp_number_4: String? = null

    @SerializedName("sp_name_5")
    @Expose
    var sp_name_5: String? = null

    @SerializedName("sp_number_5")
    @Expose
    var sp_number_5: String? = null

    @SerializedName("sp_name_6")
    @Expose
    var sp_name_6: String? = null

    @SerializedName("sp_number_6")
    @Expose
    var sp_number_6: String? = null

    @SerializedName("expected_farmers")
    @Expose
    var expected_farmers: String? = null

    @SerializedName("expected_pda")
    @Expose
    var expected_pda: String? = null

    @SerializedName("ticket_status")
    @Expose
    var ticket_status: String? = null

    @SerializedName("base_url")
    @Expose
    var base_url: String? = null

    @SerializedName("uploaded_file")
    @Expose
    var uploaded_file: String? = null
}
