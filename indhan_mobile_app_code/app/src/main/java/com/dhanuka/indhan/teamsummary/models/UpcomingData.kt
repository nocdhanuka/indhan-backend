package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class UpcomingData(
    val upcomingATP: List<TeamSummaryUpcomingATP>
) : Serializable