package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersListData

@Dao
interface InfluencersListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersListData?): Long

    @Query("SELECT * FROM InfluencersListData")
    fun allInfluencers(): List<InfluencersListData?>?

    @Query("SELECT * FROM InfluencersListData WHERE influencer_id =:id AND office_type =:officeType")
    fun allInfluencersBranchByOfficeType(id: Int, officeType: String): List<InfluencersListData?>?

    @Query("SELECT * FROM InfluencersListData WHERE parent_influencer_id =:id AND office_type =:officeType")
    fun allInfluencersBranchByOfficeTypeAndInfluencersType(
        id: Int,
        officeType: String
    ): List<InfluencersListData?>?

    @Query("SELECT COUNT(parent_influencer_id) FROM InfluencersListData WHERE parent_influencer_id =:id AND office_type =:officeType")
    fun getBranchCount(
        id: Int,
        officeType: String
    ): Int

    @Query("SELECT * FROM InfluencersListData WHERE office_type=:isHeadOffice")
    fun allInfluencersCompany(isHeadOffice: String): List<InfluencersListData?>?

    @Query("DELETE FROM InfluencersListData")
    fun deleteTableData()
}