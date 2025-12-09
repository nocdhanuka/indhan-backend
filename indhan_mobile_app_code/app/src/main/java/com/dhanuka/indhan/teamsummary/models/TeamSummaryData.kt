package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class TeamSummaryData(
    val averageVisit: Double,
    val ddCount: Int,
    val ddDetail: List<TeamSummaryDdDetail>,
    val farmerCount: Int,
    val upcomingATP: List<TeamSummaryUpcomingATP>,
    val villageCount: Int
) : Serializable