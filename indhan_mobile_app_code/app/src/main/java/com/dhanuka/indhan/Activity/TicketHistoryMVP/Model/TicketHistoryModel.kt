package com.dhanuka.indhan.Activity.TicketHistoryMVP.Model

import androidx.room.Entity
import androidx.room.Ignore
import androidx.room.Index
import androidx.room.PrimaryKey
import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName
import java.io.Serializable

/**
 * Created by John on 7/18/2018.
 */
class TicketHistoryModel {
    @SerializedName("status")
    @Expose
    var status: String? = null

    @SerializedName("status_code")
    @Expose
    var statusCode: Int? = null

    @SerializedName("message")
    @Expose
    var message: String? = null

    @SerializedName("data")
    @Expose
    var data: List<Datum>? = null

    @Entity(indices = [Index(value = ["ticket"], unique = true)])
    class Datum : Serializable {
        @JvmField
        @PrimaryKey(autoGenerate = true)
        var id = 0

        @SerializedName("tID")
        @Expose
        var tID: Int? = 0

        @SerializedName("ticket")
        @Expose
        var ticket: String = ""

        @SerializedName("dd_ticket_role")
        @Expose
        var ddTicketRole: String = ""
            get() {
                if (field.isEmpty()) {
                    field = "0"
                }
                return field
            }

        @SerializedName("datetime")
        @Expose
        var datetime: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("datetime_format")
        @Expose
        var datetimeFormat: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("description")
        @Expose
        var description: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("total_farmers")
        @Expose
        var totalFarmers: Int? = 0

        @SerializedName("total_pda")
        @Expose
        var totalPDA: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("activity_duration")
        @Expose
        var activityDuration: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("ifc_farmer_count")
        @Expose
        var ifcFarmerCount: String = ""
            get() {
                if (field == null) {
                    field = "0"
                }
                return field
            }

        @SerializedName("total_valid_farmers")
        @Expose
        var totalValidFarmers: Int? = 0

        @SerializedName("data_source")
        @Expose
        var dataSource: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("state")
        @Expose
        var state: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("district")
        @Expose
        var district: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        @SerializedName("territory")
        @Expose
        var territory: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("total_activity_distance")
        @Expose
        var total_activity_distance: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                if (field == "0") {
                    field = ""
                }
                return field
            }

        @SerializedName("tehsil")
        @Expose
        var tehsil: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("village")
        @Expose
        var village: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var village_id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("product_1")
        @Expose
        var product1: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("product_2")
        @Expose
        var product2: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("product_3")
        @Expose
        var product3: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("crop_1")
        @Expose
        var crop1: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("crop_2")
        @Expose
        var crop2: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("crop_3")
        @Expose
        var crop3: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("expected_farmers")
        @Expose
        var expectedFarmers: Int? = 0

        @SerializedName("expected_pda")
        @Expose
        var expectedPda: Int? = 0

        @SerializedName("sp_name_1")
        @Expose
        var spName1: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_1")
        @Expose
        var spNumber1: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_name_2")
        @Expose
        var spName2: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_2")
        @Expose
        var spNumber2: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_name_3")
        @Expose
        var spName3: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_3")
        @Expose
        var spNumber3: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_name_4")
        @Expose
        var spName4: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_4")
        @Expose
        var spNumber4: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_name_5")
        @Expose
        var spName5: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_5")
        @Expose
        var spNumber5: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_name_6")
        @Expose
        var spName6: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("sp_number_6")
        @Expose
        var spNumber6: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("base_url")
        @Expose
        var baseUrl: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("uploaded_file")
        @Expose
        var uploadedFile: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("ticket_status")
        @Expose
        var ticketStatus: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("language_id")
        @Expose
        var language_id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("language")
        @Expose
        var language: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("dd_name")
        @Expose
        var ddName: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("dd_id")
        @Expose
        var ddId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("user_type")
        @Expose
        var userType: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var districtId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var teritoryId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var product1Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var product2Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var product3Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var crop1Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var crop2Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var crop3Id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var tehsilId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var stateId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var dataSourceId: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var user_farmer: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var isOffline = false
        var isTicketClosed = false
        var isFarmerRegistered = false

        @Ignore
        var invited_farmer_count: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }
        var isImageUploaded = false
        var isTicketATP = false
        var plan_atp_id = 0
        var isTicketUpdated = false

        @SerializedName("feedbackImage")
        @Expose
        var feedbackImage: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("uploadedByType")
        @Expose
        var uploadedByType: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        @SerializedName("heightLightCount")
        @Expose
        var heightLightCount = 0

        @SerializedName("demo_request_id")
        @Expose
        var demo_request_id: String = ""
            get() {
                if (field == null) {
                    field = ""
                }
                return field
            }

        fun gettID(): Int? {
            return tID
        }

        fun settID(tID: Int?) {
            this.tID = tID
        }
    }
}