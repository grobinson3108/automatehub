import {
	ICredentialType,
	INodeProperties,
} from 'n8n-workflow';

export class ContentExtractorApi implements ICredentialType {
	name = 'contentExtractorApi';
	displayName = 'Content Extractor API';
	documentationUrl = 'https://automatehub.fr/docs/content-extractor';
	properties: INodeProperties[] = [
		{
			displayName: 'API Endpoint',
			name: 'endpoint',
			type: 'options',
			default: 'cloud',
			options: [
				{
					name: 'AutomateHub Cloud (Recommandé)',
					value: 'cloud',
					description: 'Utiliser le service cloud AutomateHub',
				},
				{
					name: 'Auto-hébergé',
					value: 'self',
					description: 'Utiliser votre propre instance',
				},
			],
		},
		{
			displayName: 'API Key',
			name: 'apiKey',
			type: 'string',
			typeOptions: {
				password: true,
			},
			default: '',
			required: true,
			description: 'Votre clé API Content Extractor',
			displayOptions: {
				show: {
					endpoint: ['cloud'],
				},
			},
		},
		{
			displayName: 'URL de base',
			name: 'baseUrl',
			type: 'string',
			default: '',
			required: true,
			description: 'URL de votre instance (ex: https://votre-domaine.fr/api/content-extractor)',
			displayOptions: {
				show: {
					endpoint: ['self'],
				},
			},
		},
		{
			displayName: 'API Key',
			name: 'selfApiKey',
			type: 'string',
			typeOptions: {
				password: true,
			},
			default: '',
			required: true,
			description: 'Clé API de votre instance',
			displayOptions: {
				show: {
					endpoint: ['self'],
				},
			},
		},
	];
}