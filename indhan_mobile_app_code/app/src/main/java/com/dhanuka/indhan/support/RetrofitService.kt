package com.dhanuka.indhan.support

import com.dhanuka.indhan.support.model.*
import retrofit2.Call
import retrofit2.http.*

interface RetrofitService {

    @GET("v2/getSubMenu/menu_id=14")
    fun getSubMenu(): Call<List<SupportSubMenuBean>>

    @GET("v2/getCampaign")
    fun getCampaigns(
        @Query("enc") enc: String?,
        @Query("c_type_id") c_type: Int?
    ): Call<List<CampaignModel>>

    @GET("v2/getMandCampaign")
    fun getManCampaigns(@Query("enc") enc: String?): Call<ManCampaignResponseModel>

    @GET("v2/getCampaignChatList/c_id={id}")
    fun getQA(@Path("id") id: Int?): Call<List<QuestionTypeModel>>

    @POST("v2/postResponse")
    fun postResponse(@Body response: PostData): Call<PostDataResponse>

    @POST("v2/getCampAns")
    fun checkFilled(
        @Query("ecn") enc: String?,
        @Query("campaign_id") cId: Int?
    ): Call<FilledCampaign>

}