package com.dhanuka.indhan.Activity.TicketHistoryMVP.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.List;

public class TicketHistoryAtpResponse {

    @Expose
    @SerializedName("data")
    private List<Data> data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public List<Data> getData() {
        return data;
    }

    public void setData(List<Data> data) {
        this.data = data;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getStatus_code() {
        return status_code;
    }

    public void setStatus_code(int status_code) {
        this.status_code = status_code;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public static class Data implements Serializable {
        @Expose
        @SerializedName("heightLightCount")
        private int heightLightCount;
        @Expose
        @SerializedName("duration")
        private String duration;
        @Expose
        @SerializedName("departure_longitude")
        private String departure_longitude;
        @Expose
        @SerializedName("departure_latitude")
        private String departure_latitude;
        @Expose
        @SerializedName("departure_uploaded_file")
        private String departure_uploaded_file;
        @Expose
        @SerializedName("departure_description")
        private String departure_description;
        @Expose
        @SerializedName("arival_longitude")
        private String arival_longitude;
        @Expose
        @SerializedName("arival_latitude")
        private String arival_latitude;
        @Expose
        @SerializedName("arival_time")
        private String arival_time;
        @Expose
        @SerializedName("arival_description")
        private String arival_description;
        @Expose
        @SerializedName("arival_uploaded_file")
        private String arival_uploaded_file;
        @Expose
        @SerializedName("ticket_status")
        private String ticket_status;
        @Expose
        @SerializedName("uploadedByType")
        private String uploadedByType;
        @Expose
        @SerializedName("uploaded_file")
        private String uploaded_file;
        @Expose
        @SerializedName("base_url")
        private String base_url;
        @Expose
        @SerializedName("feedbackImage")
        private String feedbackImage;
        @Expose
        @SerializedName("atpfeedback_id")
        private int atpfeedback_id;
        @Expose
        @SerializedName("longitude")
        private String longitude;
        @Expose
        @SerializedName("latitude")
        private String latitude;
        @Expose
        @SerializedName("dd_id")
        private int dd_id;
        @Expose
        @SerializedName("demo_request_id")
        private String demo_request_id;
        @Expose
        @SerializedName("departure_time")
        private String departure_time;
        @Expose
        @SerializedName("dd_name")
        private String dd_name;
        @Expose
        @SerializedName("user_type")
        private String user_type;
        @Expose
        @SerializedName("user_id")
        private int user_id;
        @Expose
        @SerializedName("language")
        private String language;
        @Expose
        @SerializedName("user_farmer")
        private String user_farmer;
        @Expose
        @SerializedName("language_id")
        private int language_id;
        @Expose
        @SerializedName("sp_number_6")
        private String sp_number_6;
        @Expose
        @SerializedName("sp_name_6")
        private String sp_name_6;
        @Expose
        @SerializedName("sp_number_5")
        private String sp_number_5;
        @Expose
        @SerializedName("sp_name_5")
        private String sp_name_5;
        @Expose
        @SerializedName("sp_number_4")
        private String sp_number_4;
        @Expose
        @SerializedName("sp_name_4")
        private String sp_name_4;
        @Expose
        @SerializedName("sp_number_3")
        private String sp_number_3;
        @Expose
        @SerializedName("sp_name_3")
        private String sp_name_3;
        @Expose
        @SerializedName("sp_number_2")
        private String sp_number_2;
        @Expose
        @SerializedName("sp_name_2")
        private String sp_name_2;
        @Expose
        @SerializedName("sp_number_1")
        private String sp_number_1;
        @Expose
        @SerializedName("sp_name_1")
        private String sp_name_1;
        @Expose
        @SerializedName("expected_pda")
        private int expected_pda;
        @Expose
        @SerializedName("expected_farmers")
        private int expected_farmers;
        @Expose
        @SerializedName("crop_3")
        private String crop_3;
        @Expose
        @SerializedName("crop_2")
        private String crop_2;
        @Expose
        @SerializedName("crop_1")
        private String crop_1;
        @Expose
        @SerializedName("product_3")
        private String product_3;
        @Expose
        @SerializedName("product_2")
        private String product_2;
        @Expose
        @SerializedName("product_1")
        private String product_1;
        @Expose
        @SerializedName("village")
        private String village;
        @Expose
        @SerializedName("tehsil")
        private String tehsil;
        @Expose
        @SerializedName("district")
        private String district;
        @Expose
        @SerializedName("state")
        private String state;
        @Expose
        @SerializedName("data_source")
        private String data_source;
        @Expose
        @SerializedName("invited_farmer_count")
        private int invited_farmer_count;
        @Expose
        @SerializedName("total_valid_farmers")
        private int total_valid_farmers;
        @Expose
        @SerializedName("total_farmers")
        private int total_farmers;
        @Expose
        @SerializedName("description")
        private String description;
        @Expose
        @SerializedName("datetime_format")
        private String datetime_format;
        @Expose
        @SerializedName("datetime")
        private String datetime;
        @Expose
        @SerializedName("plan_atp_id")
        private int plan_atp_id;
        @Expose
        @SerializedName("ticket")
        private String ticket;
        @Expose
        @SerializedName("tID")
        private int tID;

        public int getHeightLightCount() {
            return heightLightCount;
        }

        public void setHeightLightCount(int heightLightCount) {
            this.heightLightCount = heightLightCount;
        }

        public String getDuration() {
            return duration;
        }

        public void setDuration(String duration) {
            this.duration = duration;
        }

        public String getDeparture_longitude() {
            return departure_longitude;
        }

        public void setDeparture_longitude(String departure_longitude) {
            this.departure_longitude = departure_longitude;
        }

        public String getDeparture_latitude() {
            return departure_latitude;
        }

        public void setDeparture_latitude(String departure_latitude) {
            this.departure_latitude = departure_latitude;
        }

        public String getDeparture_uploaded_file() {
            return departure_uploaded_file;
        }

        public void setDeparture_uploaded_file(String departure_uploaded_file) {
            this.departure_uploaded_file = departure_uploaded_file;
        }

        public String getDeparture_description() {
            return departure_description;
        }

        public void setDeparture_description(String departure_description) {
            this.departure_description = departure_description;
        }

        public String getArival_longitude() {
            return arival_longitude;
        }

        public void setArival_longitude(String arival_longitude) {
            this.arival_longitude = arival_longitude;
        }

        public String getArival_latitude() {
            return arival_latitude;
        }

        public void setArival_latitude(String arival_latitude) {
            this.arival_latitude = arival_latitude;
        }

        public String getArival_time() {
            return arival_time;
        }

        public void setArival_time(String arival_time) {
            this.arival_time = arival_time;
        }

        public String getArival_description() {
            return arival_description;
        }

        public void setArival_description(String arival_description) {
            this.arival_description = arival_description;
        }

        public String getArival_uploaded_file() {
            return arival_uploaded_file;
        }

        public void setArival_uploaded_file(String arival_uploaded_file) {
            this.arival_uploaded_file = arival_uploaded_file;
        }

        public String getTicket_status() {
            return ticket_status;
        }

        public void setTicket_status(String ticket_status) {
            this.ticket_status = ticket_status;
        }

        public String getUploadedByType() {
            return uploadedByType;
        }

        public void setUploadedByType(String uploadedByType) {
            this.uploadedByType = uploadedByType;
        }

        public String getUploaded_file() {
            return uploaded_file;
        }

        public void setUploaded_file(String uploaded_file) {
            this.uploaded_file = uploaded_file;
        }

        public String getBase_url() {
            return base_url;
        }

        public void setBase_url(String base_url) {
            this.base_url = base_url;
        }

        public String getFeedbackImage() {
            return feedbackImage;
        }

        public void setFeedbackImage(String feedbackImage) {
            this.feedbackImage = feedbackImage;
        }

        public int getAtpfeedback_id() {
            return atpfeedback_id;
        }

        public void setAtpfeedback_id(int atpfeedback_id) {
            this.atpfeedback_id = atpfeedback_id;
        }

        public String getLongitude() {
            return longitude;
        }

        public void setLongitude(String longitude) {
            this.longitude = longitude;
        }

        public String getLatitude() {
            return latitude;
        }

        public void setLatitude(String latitude) {
            this.latitude = latitude;
        }

        public int getDd_id() {
            return dd_id;
        }

        public void setDd_id(int dd_id) {
            this.dd_id = dd_id;
        }

        public String getDemo_request_id() {
            return demo_request_id;
        }

        public void setDemo_request_id(String demo_request_id) {
            this.demo_request_id = demo_request_id;
        }

        public String getDeparture_time() {
            return departure_time;
        }

        public void setDeparture_time(String departure_time) {
            this.departure_time = departure_time;
        }

        public String getDd_name() {
            return dd_name;
        }

        public void setDd_name(String dd_name) {
            this.dd_name = dd_name;
        }

        public String getUser_type() {
            return user_type;
        }

        public void setUser_type(String user_type) {
            this.user_type = user_type;
        }

        public int getUser_id() {
            return user_id;
        }

        public void setUser_id(int user_id) {
            this.user_id = user_id;
        }

        public String getLanguage() {
            return language;
        }

        public void setLanguage(String language) {
            this.language = language;
        }

        public String getUser_farmer() {
            return user_farmer;
        }

        public void setUser_farmer(String user_farmer) {
            this.user_farmer = user_farmer;
        }

        public int getLanguage_id() {
            return language_id;
        }

        public void setLanguage_id(int language_id) {
            this.language_id = language_id;
        }

        public String getSp_number_6() {
            return sp_number_6;
        }

        public void setSp_number_6(String sp_number_6) {
            this.sp_number_6 = sp_number_6;
        }

        public String getSp_name_6() {
            return sp_name_6;
        }

        public void setSp_name_6(String sp_name_6) {
            this.sp_name_6 = sp_name_6;
        }

        public String getSp_number_5() {
            return sp_number_5;
        }

        public void setSp_number_5(String sp_number_5) {
            this.sp_number_5 = sp_number_5;
        }

        public String getSp_name_5() {
            return sp_name_5;
        }

        public void setSp_name_5(String sp_name_5) {
            this.sp_name_5 = sp_name_5;
        }

        public String getSp_number_4() {
            return sp_number_4;
        }

        public void setSp_number_4(String sp_number_4) {
            this.sp_number_4 = sp_number_4;
        }

        public String getSp_name_4() {
            return sp_name_4;
        }

        public void setSp_name_4(String sp_name_4) {
            this.sp_name_4 = sp_name_4;
        }

        public String getSp_number_3() {
            return sp_number_3;
        }

        public void setSp_number_3(String sp_number_3) {
            this.sp_number_3 = sp_number_3;
        }

        public String getSp_name_3() {
            return sp_name_3;
        }

        public void setSp_name_3(String sp_name_3) {
            this.sp_name_3 = sp_name_3;
        }

        public String getSp_number_2() {
            return sp_number_2;
        }

        public void setSp_number_2(String sp_number_2) {
            this.sp_number_2 = sp_number_2;
        }

        public String getSp_name_2() {
            return sp_name_2;
        }

        public void setSp_name_2(String sp_name_2) {
            this.sp_name_2 = sp_name_2;
        }

        public String getSp_number_1() {
            return sp_number_1;
        }

        public void setSp_number_1(String sp_number_1) {
            this.sp_number_1 = sp_number_1;
        }

        public String getSp_name_1() {
            return sp_name_1;
        }

        public void setSp_name_1(String sp_name_1) {
            this.sp_name_1 = sp_name_1;
        }

        public int getExpected_pda() {
            return expected_pda;
        }

        public void setExpected_pda(int expected_pda) {
            this.expected_pda = expected_pda;
        }

        public int getExpected_farmers() {
            return expected_farmers;
        }

        public void setExpected_farmers(int expected_farmers) {
            this.expected_farmers = expected_farmers;
        }

        public String getCrop_3() {
            return crop_3;
        }

        public void setCrop_3(String crop_3) {
            this.crop_3 = crop_3;
        }

        public String getCrop_2() {
            return crop_2;
        }

        public void setCrop_2(String crop_2) {
            this.crop_2 = crop_2;
        }

        public String getCrop_1() {
            return crop_1;
        }

        public void setCrop_1(String crop_1) {
            this.crop_1 = crop_1;
        }

        public String getProduct_3() {
            return product_3;
        }

        public void setProduct_3(String product_3) {
            this.product_3 = product_3;
        }

        public String getProduct_2() {
            return product_2;
        }

        public void setProduct_2(String product_2) {
            this.product_2 = product_2;
        }

        public String getProduct_1() {
            return product_1;
        }

        public void setProduct_1(String product_1) {
            this.product_1 = product_1;
        }

        public String getVillage() {
            return village;
        }

        public void setVillage(String village) {
            this.village = village;
        }

        public String getTehsil() {
            return tehsil;
        }

        public void setTehsil(String tehsil) {
            this.tehsil = tehsil;
        }

        public String getDistrict() {
            return district;
        }

        public void setDistrict(String district) {
            this.district = district;
        }

        public String getState() {
            return state;
        }

        public void setState(String state) {
            this.state = state;
        }

        public String getData_source() {
            return data_source;
        }

        public void setData_source(String data_source) {
            this.data_source = data_source;
        }

        public int getInvited_farmer_count() {
            return invited_farmer_count;
        }

        public void setInvited_farmer_count(int invited_farmer_count) {
            this.invited_farmer_count = invited_farmer_count;
        }

        public int getTotal_valid_farmers() {
            return total_valid_farmers;
        }

        public void setTotal_valid_farmers(int total_valid_farmers) {
            this.total_valid_farmers = total_valid_farmers;
        }

        public int getTotal_farmers() {
            return total_farmers;
        }

        public void setTotal_farmers(int total_farmers) {
            this.total_farmers = total_farmers;
        }

        public String getDescription() {
            return description;
        }

        public void setDescription(String description) {
            this.description = description;
        }

        public String getDatetime_format() {
            return datetime_format;
        }

        public void setDatetime_format(String datetime_format) {
            this.datetime_format = datetime_format;
        }

        public String getDatetime() {
            return datetime;
        }

        public void setDatetime(String datetime) {
            this.datetime = datetime;
        }

        public int getPlan_atp_id() {
            return plan_atp_id;
        }

        public void setPlan_atp_id(int plan_atp_id) {
            this.plan_atp_id = plan_atp_id;
        }

        public String getTicket() {
            return ticket;
        }

        public void setTicket(String ticket) {
            this.ticket = ticket;
        }

        public int getTID() {
            return tID;
        }

        public void setTID(int tID) {
            this.tID = tID;
        }
    }
}
