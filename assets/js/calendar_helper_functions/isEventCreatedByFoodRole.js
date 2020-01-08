export default function isEventCreatedByFoodRole(eventData)
{
    return (
        eventData &&
        eventData.createdBy &&
        eventData.createdBy.roles &&
        eventData.createdBy.roles.includes('ROLE_FOOD')
    );
}